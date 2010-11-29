

$(document).ready(function() {
    var uploads = [];
    $(".dropable").each(function(i, val){
        uploads[i] = new Upload(val.id);
    });
        
});

function Upload(element_id){

    this.dragEnter = function (evt) {
	evt.stopPropagation();
	evt.preventDefault();
    };

   this.dragExit = function (evt) {
	evt.stopPropagation();
	evt.preventDefault();
    };

    this.dragOver = function (evt) {
	evt.stopPropagation();
	evt.preventDefault();
	
    };

    this.drop = function drop(evt) {
		evt.stopPropagation();
		evt.preventDefault();
		var files = evt.dataTransfer.files;
		
		////////////// TRY FF //////////////
		var dt = evt.dataTransfer;

		var type = "text/x-moz-url-data";
		try{
			if (dt.mozGetDataAt(type, 0) !=null){
				files = [{'name':dt.mozGetDataAt(type, 0), 'type':'url' }]
			}
			//console.log(dt.mozGetDataAt(type, 0));	
		}catch(e){}	  		
		///////////////////////////////////
			
		var count = files.length;

		var _this = window.document["Upload_Instances"][this.getAttribute('handler')];
		// Only call the handler if 1 or more files was dropped.
		if (count > 0) 	_this.handleFiles(files);
    };

    this.handleFiles = function(files) {
		var file = files[0];
		var $dropbox = $(this.element_id+" .dropbox");
		var dropbox = $dropbox[0];
		dropbox.innerHTML = "Processing " + file.name;
		/*
		this.handler = this;
		if (file.type=='url') this.handleReaderLoadEnd({'target':{result:file.name}})
		*/
		var reader = new FileReader();

		// init the reader event handlers
		reader.onprogress = this.handleReaderProgress;
		reader.onloadend = this.handleReaderLoadEnd;

		reader.handler = this;

		// begin the read operation
		reader.readAsDataURL(file);
    };

    this.handleReaderProgress = function (evt) {
        
        var _this = this.handler;
	if (evt.lengthComputable) {
		var loaded = (evt.loaded / evt.total);
                _this.progressBar(loaded * 100);
	}
    };

    this.handleReaderLoadEnd = function (evt) {
        var _this = this.handler;
	    _this.progressBar(100);

        $(_this.element_id+" img.preview").attr('src', evt.target.result);
        var $dropbox = $(_this.element_id+" .dropbox");
        $dropbox.hide();
        $(_this.element_id+" img.preview").show();
        $(_this.element_id+" .delete-image").show();
    };

    this.progressBar = function (percentage){
        //console.log(percentage);
        $(this.element_id+" div .progressbar").width(percentage);
    };

    this.deleteImage = function(){
        var _this = window.document["Upload_Instances"][this.getAttribute('handler')];
        $(_this.element_id+" .dropbox").html("Drop file here...");
        $(_this.element_id+" img.preview").attr('src', '');
        $(_this.element_id+" .dropbox").show();
        $(_this.element_id+" img.preview").hide();
        $(_this.element_id+" .delete-image").hide();
    }

    this.init = function(){
        var $dropbox = $(this.element_id+" .dropbox");
        var dropbox = $dropbox[0];

        $dropbox.attr('handler', this.element_id_original);

        if (!('Upload_Instances' in window.document)) window.document["Upload_Instances"] = {};
        window.document["Upload_Instances"][this.element_id_original] = this;
        
	// init event handlers
	dropbox.addEventListener("dragenter", this.dragEnter, false);
	dropbox.addEventListener("dragexit", this.dragExit, false);
	dropbox.addEventListener("dragover", this.dragOver, false);
	dropbox.addEventListener("drop", this.drop, false);


        $(this.element_id+" .delete-image").click(this.deleteImage);
        $(this.element_id+" .delete-image").attr('handler', this.element_id_original);

        this.progressBar(0);
    };

    
    this.element_id_original = element_id;
    this.element_id = "#"+element_id;

    this.init();


}
