ZKUploader.define([ 'jquery', 'backbone' ],function($, Backbone) {
    "use strict";
    
    var DEBUG = false  //是否开启Debug信息输出
    var Connectstatus_Interval = 12 * 1000 //轮询时间间隔

    var BackupPlugin = {
        init: function(finder) {

            // 检测视图上是否存在
            function isExistBackupFolder(){
                var folders = $('a[role=treeitem]');
                for(var index=0; index<folders.length;index++){
                    var $f = $(folders[index])
                    //检测左侧栏是否存在
                    if($f.text() === 'USB'){
                        return true
                    }
                }
                return false
            }

            finder.on('app:ready', function(evt) {

                //==== 新增检测设备是否挂载
                function check_connect_status(){
                    // 请求
                    $.ajax({
                        url: 'core/connector/php/connector.php?command=Connectstatus',
                        type: 'GET',

                        success: function(res){
                            if(res.status == 'ok'){
                               //检测到存在backup目录
                                
                               if(isExistBackupFolder()){
                                 // 如果文件夹已经显示了，又检测到后台有backup目录，则不需要提示
                                 if(DEBUG) console.log('连接状态: 已连接')
                                 return;
                               }

                               finder.request( 'dialog:info', {
                                    name: 'CheckConnectStatusDialog',
                                    title: '提示',
                                    msg: '发现移动存储设备已接入',
                                    buttons: [ 'ok' ] //'cancel'
                                });
                            }else{
                                //检测到不存在backup目录
                                
                                if(isExistBackupFolder()){
                                    // 如果文件夹已经显示了，又检测到后台不存在backup目录，则提示连接断开
                                    if(DEBUG) console.log('连接状态: 连接断开')
                                    finder.request( 'dialog:info', {
                                        name: 'CheckConnectStatusDialog',
                                        title: '提示',
                                        msg: '移动存储设备已断开',
                                        buttons: [ 'ok' ] //'cancel'
                                    });
                               }
                            }
                        },
                        error: function(){
                            //http 请求错误，请检测网络/网管
                            if(DEBUG) console.log('http 请求错误，请检测网络/网管')
                        }

                    })
                }

                //轮询检测是否存在移动存储目录
                setInterval(check_connect_status.bind(this), Connectstatus_Interval)

                //点击连接提示对话框
                finder.on( 'dialog:CheckConnectStatusDialog:ok', function( evt ) {
                    //确认后刷新
                    if(DEBUG) console.log('正在刷新...')
                    window.location.reload()
                    finder.request( 'dialog:destroy' )
                } );
            });

            finder.on( 'toolbar:reset:Main:resources', function( evt ) {
                if(DEBUG) console.log('toolbar:reset:Main:resources')
                appendBackupToolbar(evt)

            }, this, null, 1000 );
            //选中文件夹时
            finder.on( 'toolbar:reset:Main:folder', function( evt ) {
                if(DEBUG) console.log('toolbar:reset:Main:folder')
                appendBackupToolbar(evt)

            }, this, null, 1000 );

            //选中文件时的菜单选择
            finder.on( 'toolbar:reset:Main:file', function( evt ) {
                if(DEBUG) console.log('toolbar:reset:Main:file')
                appendBackupToolbar(evt)

            }, this, null, 1000 );
            // 选中多个文件时
            finder.on( 'toolbar:reset:Main:files', function( evt ) {
                if(DEBUG) console.log('toolbar:reset:Main:files')
                appendBackupToolbar(evt)

            }, this, null, 1000 );

            /**
             * 添加一件备份按钮
             * @param evt
             */
            function appendBackupToolbar( evt){
                if(isExistBackupFolder()){
                    evt.data.toolbar.push( {
                        name: 'Backup',
                        label: '一键备份',
                        priority: 0,
                        icon: 'onekeybackup',
                        action: function(){
                            finder.request( 'dialog:info', {
                                name: 'ConfirmOnekeybackupDialog',
                                title: '提示',
                                msg: '一键备份：把图片和文件全部同步到移动存储设备上？',
                                buttons: [ 'ok', 'cancel' ]
                            });
                        }
                    } );
                }
            }

            // 确认一键备份对话框 点击确认
            finder.on( 'dialog:ConfirmOnekeybackupDialog:ok', function( evt ) {
                //确认后刷新
                if(DEBUG) console.log('正在备份...')
                finder.request( 'dialog:destroy' )
                finder.request( 'loader:show', { text: '获取同步数据中...' } );
                $.ajax({
                  url: 'core/connector/php/connector.php?command=Getallfiles',
                  type: 'GET',
                  success: function(res){
                    finder.fire('Backup:getallfiles:success', res)
                    finder.request( 'loader:hide' );
                  },
                  error: function(){}
                })
            } );
            
            finder.on('Backup:getallfiles:success', function(evt){
                if(DEBUG) console.log('获取到的列表 '+evt.data)
                var cpfiles = evt.data || []

                finder.request( 'dialog', {
                    name: 'BackupfileDialog',
                    title: '提示',
                    template: '<div data-role="navbar" class="ckf-upload-dropzone ui-body-a ui-navbar" tabindex="20" role="navigation" style="border: 0;"><div class="ui-content"><div class="ckf-upload-dropzone-grid"><div class="ckf-upload-dropzone-grid-a"><p id="ckf-label-301" class="ckf-upload-status">正在上传:<span class="backup-file">foo.jpg</span></p><p class="ckf-upload-progress-text" style=""><span class="ckf-upload-progress-text-files">已上传文件：<span class="backup-finish">11</span>/<span class="backup-total">20</span></span></p></div></div><div id="ckf-upload-progress"><div class="ckf-progress"><div class="ckf-progress-message ckf-hidden"></div><div class="ckf-progress-wrap ckf-progress-ok" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"><div class="ckf-progress-bar backup-progress" style="width: 0%;"></div></div></div></div></div></div>',
                    //templateModel: templateModel,
                    buttons: [ 'ok', 'cancel' ]
                } );

                finder.fire('Backup:process', {cpfiles: cpfiles, index: 0})
            });

            //备份处理
            finder.on('Backup:process', function(evt){
                var cpfiles = evt.data.cpfiles
                var index = evt.data.index

                $.ajax({
                    url: 'core/connector/php/connector.php?command=Backupfile&cpfile='+encodeURIComponent(cpfiles[index]),
                    type: 'GET',
                    success: function(res){
                        if(DEBUG) console.log(res)
                        //成功发送请求
                        if(res.status == 'ok'){
                            //更新进度信息

                            $('.backup-file').text(cpfiles[index]);
                            $('.backup-finish').text(index+1);
                            $('.backup-total').text(cpfiles.length);
                            $('.ckf-progress-bar')[0].style.width = (index+1)/cpfiles.length*100 + '%';

                            if(index+1 < cpfiles.length){
                                //未遍历完则继续
                                finder.fire('Backup:process', {cpfiles: cpfiles, index: index+1})
                            }else{
                                finder.fire('Backup:process:finish', {cpfiles: cpfiles})
                            }

                        }else{
                            //发生错误，提示
                            finder.request( 'dialog:info', {
                                name: 'NetworkErrorDialog',
                                title: '提示',
                                msg: res.msg,
                                buttons: [ 'okClose' ]
                            });
                        }
                    },
                    error: function(){
                        //网络错误
                        finder.request( 'dialog:close:BackupfileDialog')
                        finder.request( 'dialog:info', {
                            name: 'NetworkErrorDialog',
                            title: '提示',
                            msg: '网络繁忙，请稍后再试!',
                            buttons: [ 'okClose' ]
                        });
                    }
                })
            })

            //备份操作完毕
            finder.on('Backup:process:finish', function(evt){
                finder.request( 'dialog:info', {
                    name: 'BackupFinishDialog',
                    title: '提示',
                    msg: '备份完毕！',
                    buttons: [ 'okClose' ]
                });
            })

        }
    };

    return BackupPlugin;
});
