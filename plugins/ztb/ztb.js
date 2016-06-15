ZKUploader.define(function() {

  var MyPlugin = {
    init: function(finder) {
      //点击文件夹就展开             
      finder.on('folder:selected', function(evt) {
        $('.ui-btn-active').next().click();
      });
      //文件信息：日期和文件大小同一行显示
      finder.on('folder:getFiles:after', function(evt) {
        var $descs = $('.ckf-file-desc p');
        for (var i = 0; i < $descs.length; i++) {
          var e = $descs[i];
          var outerHTML = e.outerHTML;
          var innerHTML = e.innerHTML;
          var sp = innerHTML.split('<br>');
          if (sp.length === 2) {
            e.innerHTML = '<span class="jy-date">' + sp[0] + '</span><span class="jy-size" style="float: right;">' + sp[1] + '</span>';
          }
        }
      });

      //文件上传状态改变: 成功：绿  失败：红 一般：黑
      finder.on('app:ready', function(evt) {
        var $upload_container = $('#ckf-98');
        $('#ckf-98').bind('DOMSubtreeModified', function() {
          var $update_status = $('.ckf-upload-status');
          if ($update_status.innerHTML === '') {
            // console.log('remove...')
          } else {
            if ($update_status.text().indexOf('错误') !== -1) {
              $update_status.css('color', '#fa425d'); //red
              // console.log('red..')
              return;
            }
            if ($update_status.text().indexOf('完成') !== -1) {
              $update_status.css('color', 'green');
              // console.log('green..')
              return;
            }
            // console.log('black..')
            $update_status.css('color', 'black');
          }
        });
      });
      
      // => http://docs.cksource.com/ckfinder3/#!/api/CKFinder.Application-event-toolbar_reset_Main_resources
      finder.on( 'toolbar:reset:Main:resources', function( evt ) {
            updateToolbar(evt, ['Upload', 'View', 'Download', 'RenameFile', 'DeleteFiles'])
            // console.log('toolbar:reset:Main:resources')
            
        }, this, null, 1000 );
      //选中文件夹时  
      finder.on( 'toolbar:reset:Main:folder', function( evt ) {
            updateToolbar(evt, ['ShowFolders','Upload', 'CreateFolder', 'RenameFolder', 'DeleteFolder', 'Settings'])
            //console.log('toolbar:reset:Main:folder')
            
        }, this, null, 1000 );
      
      //选中文件时的菜单选择
      finder.on( 'toolbar:reset:Main:file', function( evt ) {
            updateToolbar(evt, ['Upload', 'View', 'Download', 'RenameFile', 'MoveFiles', 'DeleteFiles'])
            // console.log('toolbar:reset:Main:file')
            
        }, this, null, 1000 );
      // 选中多个文件时
      finder.on( 'toolbar:reset:Main:files', function( evt ) {
            updateToolbar(evt, ['Upload', 'View', 'Download', 'MoveFiles','DeleteFiles'])
            // console.log('toolbar:reset:Main:file')
            
        }, this, null, 1000 );
      
      function updateToolbar( evt , showToobarItems){
            showToobarItems = showToobarItems || []
            var toUpdate = [];

            evt.data.toolbar.forEach( function( button, index, toolbar ) {
              
              button.set('alwaysVisible', false)

              // button.attributes.
              // button.set('attributes', {'style': 'margin-right: 0.9rem;'})
              //添加自定义类
              button.set('className', 'tollbar-item-margin')
              
              // console.log(button.get( 'name' ))
              button.set('hidden', true)
              // if ( button.get('name') == 'ShowFolders' || button.get('name') == 'Upload' || button.get('name') == 'View' || button.get('name') == 'Download' || button.get('name') == 'RenameFile' || button.get('name') == 'DeleteFiles') {
              //     button.set('hidden', false)
                  
              // }
              if(showToobarItems.indexOf(button.get('name')) !== -1){
                 button.set('hidden', false)
              }
              toUpdate.push( button );
            } );
            

            evt.data.toolbar.remove( toUpdate );
            toUpdate.forEach(function(item){
              evt.data.toolbar.push( item );  
            })
      }
    }
  };

  return MyPlugin;
});
