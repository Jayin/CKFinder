CKFinder.define(function() {

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
          e.innerHTML = '<span class="jy-date">' + sp[0] + '</span><span class="jy-size" style="float: right;">' + sp[1] + '</span>';
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
    }
  };

  return MyPlugin;
});
