CKFinder.define(function(){var e={init:function(e){function n(e,n){n=n||[];var o=[];e.data.toolbar.forEach(function(e,t,i){e.set("alwaysVisible",!1),e.set("className","tollbar-item-margin"),e.set("hidden",!0),-1!==n.indexOf(e.get("name"))&&e.set("hidden",!1),o.push(e)}),e.data.toolbar.remove(o),o.forEach(function(n){e.data.toolbar.push(n)})}e.on("folder:selected",function(e){$(".ui-btn-active").next().click()}),e.on("folder:getFiles:after",function(e){for(var n=$(".ckf-file-desc p"),o=0;o<n.length;o++){var t=n[o],i=(t.outerHTML,t.innerHTML),l=i.split("<br>");2===l.length&&(t.innerHTML='<span class="jy-date">'+l[0]+'</span><span class="jy-size" style="float: right;">'+l[1]+"</span>")}}),e.on("app:ready",function(e){$("#ckf-98");$("#ckf-98").bind("DOMSubtreeModified",function(){var e=$(".ckf-upload-status");if(""===e.innerHTML);else{if(-1!==e.text().indexOf("错误"))return void e.css("color","#fa425d");if(-1!==e.text().indexOf("完成"))return void e.css("color","green");e.css("color","black")}})}),e.on("toolbar:reset:Main:resources",function(e){n(e,["Upload","View","Download","RenameFile","DeleteFiles"])},this,null,1e3),e.on("toolbar:reset:Main:folder",function(e){n(e,["ShowFolders","Upload","CreateFolder","RenameFolder","DeleteFolder","Settings"])},this,null,1e3),e.on("toolbar:reset:Main:file",function(e){n(e,["Upload","View","Download","RenameFile","DeleteFiles"])},this,null,1e3),e.on("toolbar:reset:Main:files",function(e){n(e,["Upload","View","Download","DeleteFiles"])},this,null,1e3)}};return e});