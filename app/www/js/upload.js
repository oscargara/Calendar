

$(document).ready(function() {
    var uploads = [];
    $(".dropable").each(function(i, val){
        eval('var data='+$(val).attr('data'));
        uploads[i] = new Upload(val.id, data);
    });
        
});

function Upload(element_id, data){

    this.dragEnter = function (jquery_evt) {
        var evt = jquery_evt.originalEvent;
        evt.stopPropagation();
        evt.preventDefault();
        $(this).filter(".dropbox").addClass("glow_box");
    };

   this.dragExit = function (jquery_evt) {
        var evt = jquery_evt.originalEvent;
        evt.stopPropagation();
        evt.preventDefault();
        $(this).filter(".dropbox").removeClass("glow_box");
    };

    this.dragOver = function (jquery_evt) {
        var evt = jquery_evt.originalEvent;
        evt.stopPropagation();
        evt.preventDefault();
        $(this).filter(".dropbox").addClass("glow_box");
    };

    this.drop = function drop(jquery_evt) {
        var evt = jquery_evt.originalEvent;
        var _this = jquery_evt.data._this;
        
        evt.stopPropagation();
        evt.preventDefault();

        if (evt.dataTransfer.getData("URL") != '' && evt.dataTransfer.getData("URL")!=undefined) {
            _this.putImage(evt.dataTransfer.getData("URL"));            
            _this.URLUpload(evt.dataTransfer.getData("URL"));
            return;
        }

        var files = evt.dataTransfer.files;
        var count = files.length;
        if (count){
            // Only call the handler if 1 or more files was dropped.
            _this.fileUpload(files[0]);
            if (count > 0) _this.handleFiles(files);
        }
        
    };

    this.handleFiles = function(files) {
        var file = files[0];
        var $dropbox = $(this.element_id+" .dropbox span.droplabel");
        
        $dropbox.html("Processing " + file.name);

        var reader = new FileReader();

        // init the reader event handlers
        reader.onprogress = this.handleReaderProgress;
        reader.onloadend = this.handleReaderLoadEnd;

        reader._this = this;

        // begin the read operation
        reader.readAsDataURL(file);
    };

    this.handleReaderProgress = function (evt) {        
        if (evt.lengthComputable) {
            var loaded = (evt.loaded / evt.total);
            //this._this.progressBar(loaded * 100);
        }
    };

    this.handleReaderLoadEnd = function (evt) {
        var _this = this._this;
        //_this.progressBar(100);
        _this.putImage(evt.target.result, false);

    };

    this.progressBar = function (percentage){
        console.log(percentage);
        $(this.element_id+" div .progressbar").width(percentage+'%');
    };

    this.deleteImage = function(jquery_evt){

        var _this = jquery_evt.data._this;

        this._this = _this;
        $.get(baseURL+'/calendar/deletePicture/'+_this.data.monthId, function(data) {
            $(_this.element_id+" .dropbox span.droplabel").html("Drop file here...");
            $(_this.element_id+" img.preview").attr('src', '');
            $(_this.element_id+" .dropbox").show();
            $(_this.element_id+" img.preview").hide();
            $(_this.element_id+" .delete-image").hide();

        });

    }

    this.putImage = function(url, priority){
        if (priority || $(this.element_id+" img.preview").attr('src')==''){
            $(this.element_id+" img.preview").attr('src', url);
        }
        
        var $dropbox = $(this.element_id+" .dropbox");
        $dropbox.hide();
        $(this.element_id+" img.preview").show();
        $(this.element_id+" .delete-image").show();        
    }


    this.fileUpload = function(file) {

        var reader = new FileReader();
        reader.onloadend = this.uploadData;
        reader.onprogress = this.uploadPercentage;
        reader._this = this;
        reader._file = file;
        reader.readAsBinaryString(file);
        return;
    }


    this.uploadPercentage = function(evt) {
        var loaded = (evt.loaded / evt.total);
        this._this.progressBar(loaded * 100);        
    }

    this.uploadData = function(evt){
      var _this = evt.target._this;
      var fileData = evt.target.result;
      var file = evt.target._file;
      var boundary = "xxxxxxxxx";
      var uri = baseURL+"/calendar/upload";

      var xhr = new XMLHttpRequest();     
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
          if ((xhr.status >= 200 && xhr.status <= 200) || xhr.status == 304) {
            if (xhr.responseText != "") {                
                _this.putImage(baseURL+xhr.responseText, true);
            }
          }
        }
      }


      var post = _this.data;
      var body = '';
      if(xhr.sendAsBinary == null) {
          post.base64=1;
          fileData = window.btoa(fileData);
      }
      for (var i in post){
          body += "--" + boundary + "\r\n";
          body += 'Content-Disposition: form-data; name="'+i+'"\r\n\r\n';
          body += post[i];
          body += "\r\n";
      }

      body += "--" + boundary + "\r\n";
      body += "Content-Disposition: form-data; name='uploadedfile'; filename='" + file.name + "'\r\n";
      body += "Content-Type: application/octet-stream\r\n\r\n";
      body += fileData + "\r\n";
      body += "--" + boundary + "--";
      
      if(xhr.sendAsBinary != null) {
            xhr.open("POST", uri, true);
            xhr.setRequestHeader("Content-Type", "multipart/form-data, boundary="+boundary); // simulate a file MIME POST request.
            xhr.setRequestHeader('Content-length', body.length);
            xhr.sendAsBinary(body);
      }else{
            xhr.open('POST', uri+'?base64=1', true);
            xhr.setRequestHeader("Content-Type", "multipart/form-data, boundary="+boundary); // simulate a file MIME POST request.
            xhr.send(body);
      }
      
      return true;
    }

    this.URLUpload = function(URL){
        
        var data_post = this.data;
        data_post.url = URL;

        $.ajax({
           type: "POST",
           url: baseURL+'/calendar/uploadURL',
           data: data_post,
           context:this,
           success: function(data){
                this.putImage(baseURL+data, true);
           }
         });

    }


    this.init = function(){
        var $dropbox = $(this.element_id+" .dropbox");

        $dropbox.attr('handler', this.element_id_original);

        // init event handlers
        $dropbox.bind( "dragenter", {_this:this}, this.dragEnter);
        $dropbox.bind( "dragexit", {_this:this}, this.dragExit);
        $dropbox.bind( "dragover", {_this:this}, this.dragOver);
        $dropbox.bind( "drop", {_this:this}, this.drop);

        $(this.element_id+" .delete-image").bind("click", {_this:this}, this.deleteImage);
        $(this.element_id+" .delete-image").attr('handler', this.element_id_original);

        this.progressBar(0);

        if(this.data.url!='') this.putImage(baseURL+this.data.url, true)
    };

    this.data = data;
    this.element_id_original = element_id;
    this.element_id = "#"+element_id;

    this.init();

}
