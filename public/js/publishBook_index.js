var aryTable = new Array(); //每個site的table
var aryCells = new Array(); //每個site的cell array
var tbody, thead, trs, siteIdx = 0;
// var aryAllCell = []; //所有的cell, 使用於檢查目前的預約狀況
var strStartDate, strEndDate;

var jsonData;
var $drpSym = $('#drpStartMonth'),
    $drpEym = $('#drpEndMonth');
var $hidMsg = $('#hidMsg');
var lastSelectedRow, lastSelectedCell;
var aryToggleCell = [], //Control selected
    aryShiftCell = [], //Shift selected
    arySelectedTitleCell = []

$(function() {
    setStartMonthSelect();
    $drpSym.on('change', function() {
        if (this.value != '0') {
            setEndMonthSelect(this.value);

            beforeGetBookingData(); //清除現在的資料，準備更新每個site的表格
            getBookingData(false);
        }
    });
    $drpEym.on('change', function() {
        var ed = new Date(this.value);
        strEndDate = dateString(ed, 'dateFull'); //預約現況表 結束日期

        beforeGetBookingData(); //清除現在的資料，準備更新每個site的表格
        getBookingData(false);
    });

    $('#entrust_id').change(function() {
        dataPublish.eid = $('#entrust_id option:selected').val();
    });

    // var firstSiteID;
    var tables = document.getElementById('table-tab-content').getElementsByTagName('table');
    //儲存每個site的table
    for (var k in tables) {
        if (!isNaN(k)) {
            aryTable.push(tables[k]);
            // if (firstSiteID == undefined)
            //     firstSiteID = aryTable[k].getAttribute('siteid')
        }
    }
    //取得目前預約狀況
    getBookingData(true);
});

function setStartMonthSelect() {
    var today = new Date();
    var todayVal = today.valueOf();
    var sd = new Date(todayVal);
    sd.setMonth(today.getMonth() - 4);
    sd.setDate(1);
    var ed = new Date(todayVal);
    ed.setMonth(sd.getMonth() + 12);

    var cd = new Date(sd.valueOf());
    while (cd < ed) {
        $drpSym.append($("<option></option>").attr('value', dateString(cd, 'dateFull')).text(yearMonth(cd)));
        cd.setMonth(cd.getMonth() + 1);
    }

    //載入頁面時的預設起始與結束日期
    var initSD = new Date(todayVal);
    initSD.setMonth(initSD.getMonth() - 1);
    strStartDate = dateString(initSD, 'dateFull'); //預約現況表 起始日期
    var initED = new Date(todayVal);
    initED.setMonth(initED.getMonth() + 1);
    strEndDate = dateString(initED, 'dateFull'); //預約現況表 結束日期
}

function setEndMonthSelect(sdVal) {
    var cd = new Date(sdVal);
    strStartDate = dateString(cd, 'dateFull'); //預約現況表 起始日期
    //
    var endVal = $drpEym.find(":selected").val();
    var ed;
    if (endVal == 0)
        ed = new Date(sdVal);
    else
        ed = new Date(endVal);
    if (cd >= ed) {
        ed.setMonth(ed.getMonth() + 12);
        strEndDate = ''; //還原 預約現況表 結束日期

        $drpEym.find('option').remove().end();
        while (cd < ed) {
            var lastDayOfMonth = new Date(cd.valueOf());
            lastDayOfMonth.setMonth(lastDayOfMonth.getMonth() + 1);
            lastDayOfMonth.setDate(lastDayOfMonth.getDate() - 1);
            $drpEym.append($("<option></option>").attr('value', dateString(lastDayOfMonth, 'dateFull')).text(yearMonth(lastDayOfMonth)));
            if (strEndDate == '')
                strEndDate = dateString(lastDayOfMonth, 'dateFull'); //預約現況表 結束日期

            cd.setMonth(cd.getMonth() + 1);
        }
    }
    // alert(strStartDate + ' ➜ ' + strEndDate);
}

function getBookingData(isFirst) {
    $.ajax({
        type: 'GET',
        url: "api/ad/book/list",
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() },
        data: {
            // site: siteID,
            sDate: strStartDate,
            eDate: strEndDate
        },
        dataType: "json",
        complete: function(jqXHR, textStatus) {
            // nowLoading(false);
            switch (jqXHR.status) {
                // var str = JSON.parse(JSON.stringify(jqXHR.responseText));
                case 200:
                    // $hidMsg.text(jqXHR.responseText);
                    //
                    jsonData = JSON.parse(jqXHR.responseText); //將資料字串存成資料json
                    refreshTabs(isFirst); //初始化每個site的table
                    break;
                default:
                    $hidMsg.text(jqXHR.responseText);
                    alert("資料取得錯誤");
                    break;
            }

        }
    });
}

function refreshTabs(isFirst) {
    for (var k in aryTable) {
        changeTableVar(k); //更換table中的element變數
        refreshBookingTable(); //初始化每個site的表格
    }

    if (isFirst) {
        changeTableVar(0); //回復到第一個site，table中的element變數
        //
        $(".droptabs").droptabs();
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            // var target = $(e.target).attr("href") // activated tab
            // var table = document.getElementById(target.replace('#', ''));
            siteIdx = $(e.target).attr("idx");
            changeTableVar(siteIdx);
            //
            lastSelectedCell = undefined;
            lastSelectedRow = undefined;
            clearSelectedCellAndData();
        });
    } else {
        changeTableVar(siteIdx); //回復到目前的site，table中的element變數
    }
}

function changeTableVar(idx) {
    var table = aryTable[idx];
    tbody = table.tBodies[0];
    thead = table.tHead.rows[0];
    trs = tbody.getElementsByTagName('tr');
}

//更新每個site的表格，並顯示目前預約狀況
function refreshBookingTable() {
    // var eDate = new Date(jsonData.end_date);
    var eDate = new Date(strEndDate);
    var aryRowIdx = [], //array index of tr
        aryRow = []; //array tr
    var aryCellOfSite = new Array(); //save 每個site的cell array

    //start column
    for (var j = 0; j < thead.cells.length; j++) { //j是thead的index，j = 0 為日期欄位
        var itemID = thead.cells[j].getAttribute('item');

        var countColumn = 1;
        var colspan = thead.cells[j].getAttribute('colspan');
        if (colspan != undefined)
            countColumn = parseInt(colspan);
        // alert('j='+j+', countColumn='+countColumn);
        //colspan
        for (var col = 1; col <= countColumn; col++) {
            // var d = new Date(jsonData.begin_date); //each tr 的日期
            var d = new Date(strStartDate); //each tr 的日期

            if (j == 0) { //j = 0 為日期欄位，沒有item屬性
                do {
                    var tr = document.createElement('tr');
                    var tdDate = document.createElement('td');
                    tdDate.setAttribute('date', dateString(d, 'date')); //date
                    tdDate.innerText = dateString(d, 'dateText');
                    tr.appendChild(tdDate);
                    //
                    aryRowIdx.push(dateString(d, 'date')); //save index of tr
                    aryRow.push(tr); //save tr
                    //
                    d.setDate(d.getDate() + 1);
                } while (d <= eDate);

            } else {
                if (isNaN(itemID) || itemID == null)
                    continue;

                var days = 0;
                //start row
                do {
                    //days
                    var keyBooking = dateString(d, 'date') + '-' + itemID + '-' + col; //date-position-colspan
                    if (jsonData.data.hasOwnProperty(keyBooking)) {
                        days = parseInt(jsonData.data[keyBooking].days);
                        createCellAppendToRow(d, itemID, col, days, jsonData.data[keyBooking], aryRowIdx, aryRow, aryCellOfSite); //tr append td
                    } else {
                        if (days == 0)
                            createCellAppendToRow(d, itemID, col, days, null, aryRowIdx, aryRow, aryCellOfSite); //tr append td
                    }
                    //date +1 day
                    d.setDate(d.getDate() + 1);
                    //days-1
                    if (days > 0)
                        days--;
                } while (d <= eDate);
            }
        } //colspan -END 
    } //column -END

    //put each row into table body
    for (var k in aryRow)
        tbody.appendChild(aryRow[k]);

    //save 每個site的cell array 到aryCells
    aryCells.push(aryCellOfSite);
}

function createCellAppendToRow(d, itemID, col, days, data, aryRowIdx, aryRow, aryCellOfSite) {
    var strDate = dateString(d, 'date');
    var key;
    for (var idx in aryRowIdx) {
        if (aryRowIdx[idx] == strDate) {
            key = idx;
            break;
        }
    }
    // alert('strDate=' + strDate);
    var tr = aryRow[key]; //get tr
    //create td
    var td = document.createElement('td');
    td.setAttribute('itemid', itemID); //position
    td.setAttribute('turn', col); //turn
    td.setAttribute('date', dateString(d, 'date')); //date
    // td.setAttribute('datetext', dateString(d, 'dateText'));
    //td set rowspan
    if (days > 0)
        td.setAttribute('rowspan', days);
    //
    td.onmousedown = function() { cellClick(this, this.parentElement, false); };
    if (data == null) {
        //可預約的cell
        aryCellOfSite.push(td); //把cell save在每個site的cell array中
    } else {
        td.innerText = data.project;
        td.onmousedown = null;
        td.style.background = data.color;
        if (data.status == 1 || data.status == 2) {
            td.className = 'project-pending';
        } else if (data.status == 3) {
            td.style.color = 'white';
            td.style.borderColor = '#555';
        }

        //滑鼠移過的泡泡資訊
        var oriTitle = data.dept == '' ? '' : data.dept + '-';
        oriTitle += data.user + '\n委刊單：' + data.project + '\n客戶：' + data.customer;
        td.setAttribute('data-original-title', oriTitle);
        td.setAttribute('data-container', 'body');
        td.setAttribute('data-toggle', 'tooltip');
        td.setAttribute('data-placement', 'top');
        $(td).tooltip();
    }
    //td鎖右鍵（mac: Ctrl+click）
    td.addEventListener('contextmenu', function(evt) {
        evt.preventDefault();
    });
    //tr鎖右鍵（mac: Ctrl+click）
    tr.addEventListener('contextmenu', function(evt) {
        evt.preventDefault();
    });
    //
    tr.appendChild(td);
}

function initTable_OLD() {
    //取得上個月第一天的日期
    var d = new Date();
    d.setDate(d.getDate() - 7);
    // d.setDate(1); //設定日為第一天
    // d.setMonth(d.getMonth() - 1); //設定月為上個月    
    //
    var countMonth = 0; //計算顯示了幾個月
    var mMonth = d.getMonth(); //記錄目前計算到幾月了
    //
    do { //start row
        var tr = document.createElement('tr');
        var tdDate = document.createElement('td');
        tdDate.innerText = dateString(d, 'dateText');
        tr.appendChild(tdDate);
        for (var j = 1; j < thead.cells.length; j++) { //因為j=0沒有item屬性，所以從1開始
            var colspan = parseInt(thead.cells[j].getAttribute('colspan'));
            for (var col = 0; col < colspan; col++) {
                var td = document.createElement('td');
                // td.setAttribute('siteid', siteID);
                td.setAttribute('itemid', thead.cells[j].getAttribute('item'));
                // td.setAttribute('itemname', thead.cells[j].innerText);
                td.setAttribute('date', dateString(d, 'date'));
                // td.setAttribute('datetext', dateString(d, 'dateText'));
                tr.appendChild(td);
                td.onmousedown = function() { cellClick(this, this.parentElement, false); };
                //td鎖右鍵（mac: Ctrl+click）
                td.addEventListener('contextmenu', function(evt) {
                    evt.preventDefault();
                });
                tr.addEventListener('contextmenu', function(evt) {
                    evt.preventDefault();
                });
                //
                aryAllCell.push(td);
            }
        }
        // tr.onmousedown =function(){RowClick(this,false);};
        tbody.appendChild(tr);
        //取得目前預約狀況所需要的起始與結束日期
        if (strStartDate == undefined)
            strStartDate = dateString(d, 'date');
        strEndDate = dateString(d, 'date');
        //下一天(next row)
        d.setDate(d.getDate() + 1);
        if (mMonth != d.getMonth()) {
            mMonth = d.getMonth();
            countMonth++;
        }
    } while (countMonth < 3); //顯示3個月份的所有日期
}

function setBookingInfo_OLD(siteID) {
    var data = jsonData.data;
    return;
    for (var k in data)
        setAllCell_OLD(cellList[k])
}

function setAllCell_OLD(oneBookingData) {
    var date = oneBookingData.date;
    var position = oneBookingData.publish_position_id;
    var entrust_status = oneBookingData.entrust_status;

    //檢查全部的cell
    for (var k in aryAllCell) {
        var cell = aryAllCell[k];
        var d = cell.getAttribute('date'); //cell 日期
        var pid = cell.getAttribute('itemid'); //cell 版位id
        // var sid = cell.getAttribute('siteid'); //cell 刊登處id

        if (date == d && position == pid) {
            var oriTitle = ''; //滑鼠移過的泡泡資訊
            for (var idx in jsonData.entrust_list) {
                var oneEntrust = jsonData.entrust_list[idx];
                if (oneEntrust.date == d && oneEntrust.position == pid) {
                    if (oriTitle.length > 0)
                        oriTitle += '\n\n';
                    if (oneEntrust.status == 1) {
                        oriTitle += '【已審核】' + oneEntrust.name + '\n客戶：' + oneEntrust.agent_customer;
                    } else {
                        oriTitle += '委刊單：' + oneEntrust.name + '\n客戶：' + oneEntrust.agent_customer;
                    }
                }
            }

            // entrust_status= '0':預約尚未額滿; '1':預約額滿; 2:預約都通過審核
            if (entrust_status == 0) {
                cell.className = 'status-count-0';
            } else if (entrust_status == 1) {
                cell.className = 'status-count-1';
                cell.onmousedown = null;
            } else if (entrust_status == 2) {
                cell.className = 'status-all-ok';
                cell.onmousedown = null;
            }

            // 有預約資料時顯示tooltip
            if (entrust_status >= 0) {
                cell.setAttribute('data-original-title', oriTitle);
                cell.setAttribute('data-container', 'body');
                cell.setAttribute('data-toggle', 'tooltip');
                cell.setAttribute('data-placement', 'top');
                $(cell).tooltip();
            } else {
                cell.className = '';
                cell.removeAttribute('data-original-title');
                cell.removeAttribute('data-container');
                cell.removeAttribute('data-toggle');
                cell.removeAttribute('data-placement');
                $(cell).tooltip('disable');
            }
            break;
        }
    }
}

//BlockUI
$(document).ajaxStart(nowLoading(true)).ajaxStop($.unblockUI);

function nowLoading(b) {
    if (b) {
        $.blockUI({
            theme: true,
            title: '',
            message: '<p style="text-align:center;">請稍候..</p>'
        });
    } else {
        $.unblockUI();
    }
}
//BlockUI--END

// disable text selection
document.onselectstart = function() {
    return false;
}

//要送出的資料
var dataPublish = {
    eid: 0,
    data: []
};

//將日期轉成年月字串
function yearMonth(date) {
    return date.getFullYear() + '年' + (date.getMonth() + 1) + '月';
}

//將日期轉成字串
function dateString(date, type) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    switch (type) {
        case 'dateFull':
            return year + "-" + (month < 10 ? '0' + month : month) + "-" + (day < 10 ? '0' + day : day);
        case 'dateText':
            return year + "-" + month + "-" + day;
        case 'date':
            return year + (month < 10 ? '0' + month : month) + (day < 10 ? '0' + day : day);
    }
}

//每個cell click的資料處理，可用Control 與 Shift
function cellClick(currenttd, currenttr, lock) {
    if (window.event.ctrlKey) {
        toggleCell(currenttd, currenttr, true);
        setSelectedResult();
    }

    if (window.event.button === 0) {
        if (!window.event.ctrlKey && !window.event.shiftKey) {
            // if (lastSelectedCell != currenttd)
            //     clearSelectedCellAndData();
            // refreshBookingInfo();
            toggleCell(currenttd, currenttr, false);
            setSelectedResult();
        }

        if (window.event.shiftKey) {
            // clearSelectedCellAndData();
            // refreshBookingInfo();
            // alert([lastSelectedRow.rowIndex, currenttr.rowIndex]+'\n\n'+ [lastSelectedCell.cellIndex, currenttd.cellIndex]);
            //********
            // var cellIdx;
            // for (var i = 0; i < thead.cells.length; i++) {
            //     var itemId = thead.cells[i].getAttribute('item');
            //     if (thead.cells[i].getAttribute('item') == currenttd.getAttribute('itemid')) {
            //         cellIdx = i;
            //         break;
            //     }
            // }

            shiftCell(currenttd);
            // selectCellsBetweenRows([lastSelectedRow.rowIndex, currenttr.rowIndex], [lastSelectedCell.getAttribute('item-idx'), cellIdx])
            // selectTitlesBetweenRows([lastSelectedRow.rowIndex, currenttr.rowIndex], [lastSelectedCell.getAttribute('item-idx'), currenttd.getAttribute('item-idx')]);
            setSelectedResult();
        }
    }
}

function toggleCell(cell, row, isControlClick) {
    if (isControlClick) {
        cell.className = 'selected'; //cell已選擇
        if (lastSelectedCell != cell) {
            aryToggleCell.push(cell); //save 選擇的cell
            toggleCell_SelectedTitleCell(cell, row); //選擇的cell 的 版位與日期
        }
    } else {
        clearSelectedCellAndData();
        cell.className = 'selected';
        aryToggleCell.push(cell); //save 選擇的cell
        toggleCell_SelectedTitleCell(cell, row); //選擇的cell 的 版位與日期
    }
    lastSelectedCell = cell;
    lastSelectedRow = row;
}

function toggleCell_SelectedTitleCell(cell, row) {
    var c;
    //取得 選擇的版位
    for (var i = 0; i < thead.cells.length; i++) {
        var itemId = thead.cells[i].getAttribute('item');
        if (itemId == cell.getAttribute('itemid')) {
            c = thead.cells[i];
            break;
        }
    }
    c.className = 'selected-item'; //凸顯 選擇的版位
    arySelectedTitleCell.push(c); //save 選擇的版位

    c = row.cells[0]; //取得 選擇的日期
    c.className = 'selected-date'; //凸顯 選擇的日期
    arySelectedTitleCell.push(c); //save 選擇的日期
}

function toggleCell_OLD(cell, row, isNotKeyDown) {
    var selectedItemClassName = '',
        selectedDateClassName = '';
    // cell.className = cell.className == 'selected' ? '' : 'selected';
    if (lastSelectedCell == cell) {
        cell.className = cell.className == 'selected' ? '' : 'selected';
        toggleCellDelete(cell);
        lastSelectedCell = undefined;
        lastSelectedRow = undefined;
    } else {
        if (isNotKeyDown) {
            clearSelectedCellAndData();
            cell.className = 'selected';
            aryToggleCell.push(cell);
            selectedItemClassName = 'selected-item';
            selectedDateClassName = 'selected-date';
        } else {
            if (cell.className == 'selected') {
                cell.className = '';
                toggleCellDelete(cell);
                lastSelectedCell = undefined;
                lastSelectedRow = undefined;
            } else {
                cell.className = 'selected';
                aryToggleCell.push(cell);
                selectedItemClassName = 'selected-item';
                selectedDateClassName = 'selected-date';
            }
        }

        lastSelectedCell = cell;
        lastSelectedRow = row;
        //

    }
    //*******shift選擇後再用ctrl反選擇，selected-item and selected-date都會消失
    var c;
    //取得選擇的 版位
    for (var i = 0; i < thead.cells.length; i++) {
        var itemId = thead.cells[i].getAttribute('item');
        if (itemId == cell.getAttribute('itemid')) {
            c = thead.cells[i];
            break;
        }
    }
    //凸顯選擇的 版位
    c.className = selectedItemClassName;
    arySelectedTitleCell.push(c);
    //凸顯選擇的 日期
    c = row.cells[0];
    c.className = selectedDateClassName;
    arySelectedTitleCell.push(c);
}

function toggleCellDelete(cell) {
    //aryToggleCell
    for (var i = 0; i < aryToggleCell.length; i++) {
        if (aryToggleCell[i].getAttribute('itemid') == cell.getAttribute('itemid') &&
            aryToggleCell[i].getAttribute('turn') == cell.getAttribute('turn') &&
            aryToggleCell[i].getAttribute('date') == cell.getAttribute('date')) {
            aryToggleCell.splice(i, 1);
            break;
        }
    }
    //aryShiftCell
    for (var i = 0; i < aryShiftCell.length; i++) {
        if (aryShiftCell[i].getAttribute('itemid') == cell.getAttribute('itemid') &&
            aryShiftCell[i].getAttribute('turn') == cell.getAttribute('turn') &&
            aryShiftCell[i].getAttribute('date') == cell.getAttribute('date')) {
            aryShiftCell.splice(i, 1);
            break;
        }
    }
}

function shiftCell(currentCell) {
    clearSelectedCellAndData();
    if (lastSelectedCell == undefined || currentCell == undefined || typeof lastSelectedCell.getAttribute !== "function" || typeof currentCell.getAttribute !== "function")
        return;

    var ary, sDate, eDate, sItem, eItem, sItemTurn, eItemTurn;
    //date 排序
    ary = new Array(lastSelectedCell.getAttribute('date'), currentCell.getAttribute('date'));
    ary.sort(function(a, b) {
        return a - b;
    });
    sDate = ary[0], eDate = ary[1];
    //itemTurn 排序
    sItem = lastSelectedCell.getAttribute('itemid');
    eItem = currentCell.getAttribute('itemid');
    ary = new Array(sItem.concat(lastSelectedCell.getAttribute('turn')), eItem.concat(currentCell.getAttribute('turn')));
    ary.sort(function(a, b) {
        return a - b;
    });
    sItemTurn = parseInt(ary[0]), eItemTurn = parseInt(ary[1]);

    // alert(sDate + ', ' + eDate + ', ' + sItem + ', ' + eItem + ', ' + sTurn + ', ' + eTurn);

    var aryCellOfSite = aryCells[siteIdx];
    for (var k in aryCellOfSite) {
        var cell = aryCellOfSite[k];
        var bDate = bItemTurn = false;
        if (sDate <= cell.getAttribute('date') && eDate >= cell.getAttribute('date'))
            bDate = true;
        // if (sItem <= cell.getAttribute('itemid') && eItem >= cell.getAttribute('itemid'))
        //     bItem = true;
        var itemid_turn = parseInt(cell.getAttribute('itemid').concat(cell.getAttribute('turn')));
        if (sItemTurn <= itemid_turn && eItemTurn >= itemid_turn)
            bItemTurn = true;
        if (bDate && bItemTurn) {
            // alert(cell.getAttribute('itemid') + ', ' + cell.getAttribute('date'));
            cell.className = 'selected';
            aryShiftCell.push(cell);
            //凸顯選擇的 日期
            for (var i in trs) {
                if (trs[i].cells != undefined && trs[i].cells.length > 0) {
                    var c = trs[i].cells[0];
                    if (c.getAttribute('date') == cell.getAttribute('date')) {
                        c.className = 'selected-date';
                        arySelectedTitleCell.push(c);
                    }
                }
            }
            //凸顯選擇的 版位
            for (var i in thead.cells) {
                var c = thead.cells[i];
                if (typeof c.getAttribute === "function" && c.getAttribute('item') == cell.getAttribute('itemid')) {
                    thead.cells[i].className = 'selected-item';
                    arySelectedTitleCell.push(thead.cells[i]);
                }
            }
        }

    }
}

function selectCellsBetweenRows_OLD(rowIndexes, cellIndexes) {
    rowIndexes.sort(function(a, b) {
        return a - b;
    });
    cellIndexes.sort(function(a, b) {
        return a - b;
    });

    for (var i = rowIndexes[0]; i <= rowIndexes[1]; i++) {
        for (var j = cellIndexes[0]; j <= cellIndexes[1]; j++) {
            var c = trs[i - 1].cells[j];
            c.className = 'selected';
            aryShiftCell.push(c);
        }
        // trs[i - 1].className = 'selected';
    }
}

function selectTitlesBetweenRows_OLD(rowIndexes, cellIndexes) {
    rowIndexes.sort(function(a, b) {
        return a - b;
    });
    cellIndexes.sort(function(a, b) {
        return a - b;
    });

    for (var i = rowIndexes[0]; i <= rowIndexes[1]; i++) {
        trs[i - 1].cells[0].className = 'selected-date';
        arySelectedTitleCell.push(trs[i - 1].cells[0]);

    }
    for (var i = cellIndexes[0]; i <= cellIndexes[1]; i++) {

        thead.cells[i].className = 'selected-item';
        arySelectedTitleCell.push(thead.cells[i]);
    }
}

function setSelectedResult() {
    dataPublish.data.splice(0, dataPublish.data.length);
    //Shift selected
    for (var i = 0; i < aryShiftCell.length; i++) {
        var d = {
            position: aryShiftCell[i].getAttribute('itemid'), //position id string
            turn: aryShiftCell[i].getAttribute('turn'), //turn string
            date: aryShiftCell[i].getAttribute('date'),
            // datetext: aryShiftCell[i].getAttribute('datetext')
        };
        dataPublish.data.push(d);
    }
    //click and Control click selected
    for (var i = 0; i < aryToggleCell.length; i++) {
        var d = {
            position: aryToggleCell[i].getAttribute('itemid'), //position id string
            turn: aryToggleCell[i].getAttribute('turn'), //turn string
            date: aryToggleCell[i].getAttribute('date'),
            // datetext: aryToggleCell[i].getAttribute('datetext')
        };
        dataPublish.data.push(d);
    }
    dataPublish.eid = $('#entrust_id option:selected').val();
    $hidMsg.text(JSON.stringify(dataPublish));
}

function clearSelectedCellAndData() {
    for (var i = 0; i < aryToggleCell.length; i++)
        aryToggleCell[i].className = '';
    for (var i = 0; i < aryShiftCell.length; i++)
        aryShiftCell[i].className = '';
    for (var i = 0; i < arySelectedTitleCell.length; i++)
        arySelectedTitleCell[i].className = '';

    aryToggleCell.splice(0, aryToggleCell.length); //清除 Control selected
    aryShiftCell.splice(0, aryShiftCell.length); //清除 Shift selected
    arySelectedTitleCell.splice(0, arySelectedTitleCell.length);
    dataPublish.data.splice(0, dataPublish.data.length); //清除要送出的資料
}

function beforeGetBookingData() {
    aryCells.splice(0, aryShiftCell.length); //清除 每個site的cell array
    $(tbody).empty(); //清除 table body
    lastSelectedCell = undefined;
    lastSelectedRow = undefined;
    clearSelectedCellAndData(); //清除 已選擇的cell
}

//送出預約
function bookSubmit() {
    if (dataPublish.eid == 0) {
        alert('請選擇 委刊單');
        $('select[name=entrust_id]').focus();
        return;
    }
    if (dataPublish.data.length == 0) {
        alert('尚未預約');
        return;
    }

    nowLoading(true);
    $.ajax({
        type: 'POST',
        url: "api/ad/book",
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() },
        data: dataPublish,
        dataType: "json",
        complete: function(jqXHR, textStatus) {
            switch (jqXHR.status) {
                case 200:
                    // alert(jqXHR.responseText);
                    alert('預約完成');
                    //
                    $hidMsg.text(jqXHR.responseText);
                    document.body.scrollIntoView(true);

                    beforeGetBookingData(); //清除現在的資料，準備更新每個site的表格
                    getBookingData(false);
                    break;
                default:
                    // $hidMsg.text(jqXHR.responseText);
                    alert('預約失敗');
                    break;
            }

        }
    });
}
