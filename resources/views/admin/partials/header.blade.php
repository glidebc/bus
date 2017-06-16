<!DOCTYPE html>
<html lang="zh_TW">

<head>
    <meta charset="utf-8">
    <title>
      <?php
        $fncTitle = '';
        foreach($menus as $menu)
            foreach($menu['children'] as $child)
                if($child->name == str_replace('Controller','',explode("@",class_basename(app('request')->route()->getAction()['controller']))[0])) {
                    $fncTitle .= $child->title; break;
                }
        if(strlen($fncTitle) > 0)
          echo $fncTitle.' - ';
      ?>{{ trans('quickadmin::admin.partials-header-title') }}
    </title>

    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport"/>
    <meta http-equiv="Content-type"
          content="text/html; charset=utf-8">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all"
          rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet"
          href="{{ url('quickadmin/css') }}/font-awesome.min.css"/>
    <link rel="stylesheet"
          href="{{ url('quickadmin/css') }}/bootstrap.min.css"/>
    <link rel="stylesheet"
          href="{{ url('quickadmin/css') }}/components.css"/>
    <link rel="stylesheet"
          href="{{ url('quickadmin/css') }}/quickadmin-layout.css"/>
    <link rel="stylesheet"
          href="{{ url('quickadmin/css') }}/quickadmin-theme-default.css"/>
    <link rel="stylesheet"
          href="https://code.jquery.com/ui/1.11.3/themes/hot-sneaks/jquery-ui.css">
    <link rel="stylesheet"
          href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.standalone.min.css"/>
    <style type="text/css">
      @font-face {
        font-family: 'cwTeXHei';
        font-style: normal;
        font-weight: 500;
        src: url(//fonts.gstatic.com/ea/cwtexhei/v3/cwTeXHei-zhonly.eot);
        src: url(//fonts.gstatic.com/ea/cwtexhei/v3/cwTeXHei-zhonly.eot?#iefix) format('embedded-opentype'),
             url(//fonts.gstatic.com/ea/cwtexhei/v3/cwTeXHei-zhonly.woff2) format('woff2'),
             url(//fonts.gstatic.com/ea/cwtexhei/v3/cwTeXHei-zhonly.woff) format('woff'),
             url(//fonts.gstatic.com/ea/cwtexhei/v3/cwTeXHei-zhonly.ttf) format('truetype');
      }
    </style>
</head>

<body class="page-header-fixed">