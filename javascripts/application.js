/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


OC = {
    formData : {},
    initAdmin : function(){
        jQuery(document).ready(function(){
            jQuery('#admin-accordion').accordion();
        });
    },
    
    
    initSeries : function(){
        jQuery(document).ready(function(){
            if( jQuery('#select-series').data("unconnected") !== 1 ) {
                jQuery('.series_select').attr("disabled", true);
                jQuery('.form_submit').children().attr("disabled", true);
                $('#admin-accordion').accordion({ active: 1,
                                                  autoHeight: false,
                                                  clearStyle: true });
            } else {
                jQuery('#admin-accordion').accordion({autoHeight: false,
                                                      clearStyle: true });
            }
        })
    }/*,
    
    initUpload : function(maxChunk){
        jQuery(document).ready(function(){
            $('#btn_accept').click(function() {
                OC.formData.submit();
                return false;
            });
            
            $('#video_upload').fileupload({
                limitMultiFileUploads: 1,
                autoupload: false,
                maxChunkSize: maxChunk,
                add: function(e, data) {
                    var file = data.files[0];
                    $('#total_file_size').attr('value', file.size);
                    $('#file_name').attr('value', file.name);
                    $('#upload_info').html('<p>Name: ' 
                                                + file.name 
                                                + '<br />Gr��e: ' 
                                                + OC.getFileSize(file.size) 
                                                + '</p>');
                    $('#upload_info').val(file.name);
                    OC.formData = data;
                    return false;
                },
                submit: function (e, data) {
                    $( "#progressbar" ).progressbar({
                        value: 0
                    });
                },
                progressall: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $( "#progressbar" ).progressbar( "value", progress);
                },
                done: function(e, data) {
                    $( "#progressbar" ).progressbar('destroy');
                    $('#video_upload').val('');
                }
            });
            $('#recordDate').datepicker({
                dateFormat: "yy-mm-dd"
            });
        })
    },
    getFileSize: function(input) {
        if(input/1024 > 1) {
            var inp_kb = Math.round((input/1024)*100)/100
            if(inp_kb/1024 > 1) {
                var inp_mb = Math.round((inp_kb/1024)*100)/100
                if(inp_mb/1024 > 1) {
                    var inp_gb = Math.round((inp_mb/1024)*100)/100
                    return inp_gb + 'GB';
                }
                return inp_mb + 'MB';
            }
            return inp_kb + 'KB';
        }
        return input + 'Bytes'
    } */
};

