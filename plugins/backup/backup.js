ZKUploader.define(function() {

    var BackupPlugin = {
        init: function(finder) {

            // 检测视图上是否存在
            function isExistBackupFolder(){
                var folders = $('a[role=treeitem]');
                for(var index=0; index<folders.length;index++){
                    if(folders[index].text == '移动存储'){
                        return true
                    }
                }
                return false
            }

            finder.on('app:ready', function(evt) {

                //==== 新增检测设备是否在
                function check_connect_status(){
                    // 请求
                    $.ajax({
                        url: '/ckfinder/core/connector/php/connector.php?command=Connectstatus',
                        type: 'GET',

                        success: function(res){
                            if(res.status == 'ok'){
                               //检测到存在backup目录
                                
                               if(isExistBackupFolder()){
                                 // 如果文件夹已经显示了，又检测到后台有backup目录，则不需要提示
                                 console.log('连接状态: 已连接')
                                 return;
                               }

                               finder.request( 'dialog:info', {
                                    name: 'CheckConnectStatusDialog',
                                    title: '提示',
                                    msg: '发现移动存储已接入,立即刷新?',
                                    buttons: [ 'ok', 'cancel' ]
                                });
                            }else{
                                //检测到不存在backup目录
                                
                                if(isExistBackupFolder()){
                                    // 如果文件夹已经显示了，又检测到后台不存在backup目录，则提示连接断开
                                    console.log('连接状态: 连接断开')
                                    finder.request( 'dialog:info', {
                                        name: 'CheckConnectStatusDialog',
                                        title: '提示',
                                        msg: '移动存储已断开,立即刷新?',
                                        buttons: [ 'ok', 'cancel' ]
                                    });
                               }
                            }
                        },
                        error: function(){
                            //http 请求错误，请检测网络
                            console.log('请求失败')
                        }

                    })
                }

                //finder.request( 'loader:show', { text: '轮询中...' } );

                //轮询检测是否存在移动存储目录
                setInterval(check_connect_status.bind(this), 8000)

                //点击连接提示对话框
                finder.on( 'dialog:CheckConnectStatusDialog:ok', function( evt ) {
                    //确认后刷新
                    console.log('正在刷新...')
                    window.location.reload()
                    finder.request( 'dialog:destroy' )
                } );
            });


            // => http://docs.cksource.com/ckfinder3/#!/api/CKFinder.Application-event-toolbar_reset_Main_resources
            finder.on( 'toolbar:reset:Main:resources', function( evt ) {
                console.log('toolbar:reset:Main:resources')
                appendBackupToolbar(evt)

            }, this, null, 1000 );
            //选中文件夹时
            finder.on( 'toolbar:reset:Main:folder', function( evt ) {
                console.log('toolbar:reset:Main:folder')
                appendBackupToolbar(evt)

            }, this, null, 1000 );

            //选中文件时的菜单选择
            finder.on( 'toolbar:reset:Main:file', function( evt ) {
                console.log('toolbar:reset:Main:file')
                appendBackupToolbar(evt)

            }, this, null, 1000 );
            // 选中多个文件时
            finder.on( 'toolbar:reset:Main:files', function( evt ) {
                console.log('toolbar:reset:Main:files')
                appendBackupToolbar(evt)

            }, this, null, 1000 );

            /**
             * 添加一件备份按钮
             * @param evt
             */
            function appendBackupToolbar( evt){
                if(isExistBackupFolder()){
                    evt.data.toolbar.push( {
                        name: 'Feedback',
                        label: 'Send Feedback',
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
                console.log('正在备份...')
                //window.location.reload()
                finder.request( 'dialog:destroy' )
            } );

        }
    };

    return BackupPlugin;
});
