var aryTable = new Array(); //每個site的table
var aryCells = new Array(); //每個site的cell array
var tbody, thead, trs, siteIdx = 0;
var strStartDate, strEndDate; //月份區間

var jsonData;
var $drpSym = $('#drpStartMonth'),
    $drpEym = $('#drpEndMonth');
var $hidMsg = $('#hidMsg'),
    $hidVal = $('#hidVal');
var lastSelectedRow, lastSelectedCell;
var aryToggleCell = [], //Control selected
    aryShiftCell = [], //Shift selected
    arySelectedTitleCell = []

$(function() {
    initSelectYM(); //載入頁面時的預設起始與結束日期
    $drpSym.on('change', function() { //Sym - Start Year Month
        if (this.value != '0') {
            strStartDate = this.value;
            setSelectYM(this.value, 's'); //set select year month
            // alert('SD = ' + strStartDate + ', ED = ' + strEndDate);
            getBookingData(false);
        }
    });
    $drpEym.on('change', function() { //Eym - End Year Month
        strEndDate = this.value;
        // alert('SD = ' + strStartDate + ', ED = ' + strEndDate);
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
            aryTable[k] = tables[k];
            // aryTable.push(tables[k]);
        }
    }
    //取得目前預約狀況
    getBookingData(true);
});

function initSelectYM() {
    var cd = new Date();
    var sd = new Date(cd.valueOf());
    sd.setMonth(sd.getMonth() - 1);
    strStartDate = dateString(sd, 'dateFull'); //預約現況表 起始日期
    var ed = new Date(cd.valueOf());
    ed.setMonth(ed.getMonth() + 1);
    strEndDate = dateString(ed, 'dateFull'); //預約現況表 結束日期

    setSelectYM(cd.valueOf());
}

function setSelectYM(txtSD, type) {
    var sd, ed, cd;
    //起始日期下拉選單
    if (type != undefined)
        $drpSym.empty(); //clear dropdownlist
    //sd
    sd = new Date(txtSD);
    sd.setDate(1);
    //first date(cd) and last date(ed) of list
    cd = new Date(sd.valueOf());
    cd.setMonth(cd.getMonth() - 4); //第一個年月 = sd-4個月
    // cd.setDate(1); //cd的月份的第一天
    ed = new Date(sd.valueOf());
    ed.setMonth(ed.getMonth() + 8); //最後一個年月 = sd+8個月
    //set select options
    while (cd <= ed) {
        $option = $("<option></option>").attr('value', dateString(cd, 'dateFull')).text(yearMonth(cd));
        if (dateString(cd, 'dateFull') == txtSD)
            $option.attr('selected', true);
        $drpSym.append($option);
        cd.setMonth(cd.getMonth() + 1);
    }

    //結束日期下拉選單
    if (type != undefined) {
        // var firstDate_Eym = $drpEym.find('option:first').val();
        // var lastDate_Eym = $drpEym.find('option:last').val();

        //first date(cd) and last date(ed) of list
        cd = new Date(sd.valueOf());
        cd = getLastDayOfDate(cd); //取得cd所屬月份的最後一天
        ed = new Date(sd.valueOf());
        ed.setMonth(ed.getMonth() + 12); //結束年月下拉選單 的 最後年月 = cd+12個月
        //檢查下拉選單是否包含結束日期
        var endDateSelectedIndex = 0,
            idx = -1;
        while (cd <= ed) {
            idx++;
            if (dateString(cd, 'dateFull') == strEndDate) {
                endDateSelectedIndex = idx; //
                break;
            }
            //cd +1 month
            cd.setDate(1);
            cd.setMonth(cd.getMonth() + 1);
            cd = getLastDayOfDate(cd); //取得cd所屬月份的最後一天
        }
        // alert('idx = ' + endDateSelectedIndex);

        //set end-date-dropdownlist
        $drpEym.empty(); //clear dropdownlist

        if (!endDateSelectedIndex)
            strEndDate = ''; //改變結束日期

        //reset first date(cd) of list
        cd = new Date(sd.valueOf());
        cd = getLastDayOfDate(cd); //取得cd所屬月份的最後一天
        while (cd <= ed) {
            //set 結束日期
            if (strEndDate == '')
                strEndDate = dateString(cd, 'dateFull');
            //append option
            $drpEym.append($("<option></option>").attr('value', dateString(cd, 'dateFull')).text(yearMonth(cd)));
            //cd +1 month
            cd.setDate(1);
            cd.setMonth(cd.getMonth() + 1);
            cd = getLastDayOfDate(cd); //取得cd所屬月份的最後一天
        }
        //set option selected
        if (endDateSelectedIndex)
            $drpEym.find('option:eq(' + endDateSelectedIndex + ')').attr('selected', true);

    }
}
//取得某日期所屬月份的最後一天的日期
function getLastDayOfDate(date) {
    var d = new Date(date.valueOf());
    // d.setDate(1);
    d.setMonth(d.getMonth() + 1);
    d.setDate(d.getDate() - 1);
    return d;
}

function getBookingData(isFirst) {
    // alert('SD = ' + strStartDate + ', ED = ' + strEndDate);
    nowLoading(true);
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
            // nowLoading(false);
        }
    });
}

function refreshTabs(isFirst) {
    beforeRefreshBookingTable(); //準備更新每個site的表格
    for (var k in aryTable) {
        changeTableVar(k); //更換table中的element變數
        // alert('k=' + k);
        refreshBookingTable(k); //更新每個site的表格，並顯示目前預約狀況
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

//準備更新每個site的表格
function beforeRefreshBookingTable() {
    aryCells.splice(0, aryCells.length); //清除 每個site的cell array
    lastSelectedCell = undefined;
    lastSelectedRow = undefined;
    clearSelectedCellAndData(); //清除 已選擇的cell
}

function changeTableVar(idx) {
    var table = aryTable[idx];
    tbody = table.tBodies[0];
    thead = table.tHead.rows[0];
    trs = tbody.getElementsByTagName('tr');
}

//更新每個site的表格，並顯示目前預約狀況
function refreshBookingTable(idxTable) {
    $(tbody).empty(); //清除 table body

    var eDate = new Date(strEndDate);
    var aryRow = []; //array tr
    aryCells[idxTable] = []; //每個site的cell array

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
                    // aryRowIdx.push(dateString(d, 'date')); //save index of tr
                    aryRow[dateString(d, 'date')] = tr; //save tr, key=date
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
                        createCellAppendToRow(d, itemID, col, days, jsonData.data[keyBooking], aryRow, idxTable); //tr append td
                    } else {
                        if (days == 0)
                            createCellAppendToRow(d, itemID, col, days, null, aryRow, idxTable); //tr append td
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
}

function createCellAppendToRow(d, itemID, col, days, data, aryRow, idxTable) {
    var strDate = dateString(d, 'date');
    var tr = aryRow[strDate]; //get tr
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
        aryCells[idxTable].push(td); //把cell save在每個site的cell array中
    } else {
        td.innerText = data.project;
        td.onmousedown = null;
        td.style.background = data.bgcolor;
        td.style.color = data.color;
        if (data.status == 1 || data.status == 2) {
            td.className = 'project-pending';
        } else if (data.status == 3) {
            // td.style.color = 'white';
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
            return year + "-" + (month > 9 ? month : '0' + month) + "-" + (day < 10 ? '0' + day : day);
        case 'dateText':
            return year + "-" + month + "-" + day;
        case 'date':
            return year + '' + (month > 9 ? month : '0' + month) + '' + (day < 10 ? '0' + day : day);
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
            toggleCell(currenttd, currenttr, false);
            setSelectedResult();
        }

        if (window.event.shiftKey) {
            shiftCell(currenttd);
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

function shiftCell(currentCell) {
    clearSelectedCellAndData();
    //*****
    if (currentCell == undefined) alert('currentCell undefined');
    if (typeof currentCell.getAttribute !== "function") alert('currentCell no getAttribute');

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
    // alert('L = '+aryCells.length);

    for (var k in aryCellOfSite) {
        var cell = aryCellOfSite[k];
        var bDate = bItemTurn = false;
        if (sDate <= cell.getAttribute('date') && eDate >= cell.getAttribute('date'))
            bDate = true;
        // if (sItem <= cell.getAttribute('itemid') && eItem >= cell.getAttribute('itemid'))
        //     bItem = true;
        var itemid_turn = parseInt(cell.getAttribute('itemid').concat(cell.getAttribute('turn')));

        // if(cell.getAttribute('itemid') == '1' && cell.getAttribute('turn') == '1' && cell.getAttribute('date') == '20170606')
        //     alert('itemid_turn = '+itemid_turn);//***********

        if (sItemTurn <= itemid_turn && itemid_turn <= eItemTurn)
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
    $hidVal.val(JSON.stringify(dataPublish));
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
    $hidVal.val('');
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

                    // beforeGetBookingData(); //清除現在的資料，準備更新每個site的表格
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
