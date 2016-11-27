// JavaScript Document
var $=jQuery.noConflict();

var isAjaxLoaded=false;

var pathToAD=DOCUMENT_ROOT+"/php/ajaxDispatcher.php";

var GOOGLEAPIKEY="AIzaSyC4RLVWRv4pJM3ytIohT6uiis0R46gUXzY";

var ajaxLoader="<div class='ajaxLoader left50 top50 abs'><img src='images/ajax-loader.png' class='ajaxLoader' /></div>";

var templatePopup1="<div class='popup fullwidth left50 top50 abs'><div class='closeOverlay abs'><a href='#' class='relative'>X</a></div><div class='insideContent round centered'><div class='infoComingSoon'><div data-type='content-replace'></div></div></div></div>";

var templatePopup="<div class='popup left50 top50 abs'><div class='insideContent centered'><div class='infoComingSoon infoDeleteItem'><h4 class='centered'><span id='extraReplaceIntroText'>Da li ste sigurni da želite obrisati?</span></h4><div data-type='content-replace'></div></div></div></div>";
var templateButtons="<div class='buttonsHolder'><div class='left50'><button class='mf-button btn-danger' data-action='decide' data-id='0' data-context='remove'>DA</button><button class='mf-button bg-gray' data-action='cancel' data-id='0' data-context='cancel'>NE</button></div><div class='noFloat'></div></div>";
var templateUploader="<div class='newfileUploader'><div class='qq-upload-button'><button type='button' class='mf-button abs'>Browse</button><div class='mf-input abs'></div><input id='fileupload1' class='mf-file abs' type='file' name='files[]' multiple /></div></div>";
var templateGMap="<iframe id='gMap' width='100%' height='362' frameborder='0' style='border:0' allowfullscreen></iframe>";


function getBrowserHeight()
{
	if (window.innerHeight)
	{
		return window.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight != 0)
	{
		return document.documentElement.clientHeight;
	}
	else if (document.body)
	{
		return document.body.clientHeight;
	}
	
	return 0;
};

function getBrowserWidth()
{
	if (window.innerWidth)
	{
		return window.innerWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth != 0)
	{
		return document.documentElement.clientWidth;
	}
	else if (document.body)
	{
		return document.body.clientWidth;
	}
	
	return 0;
};


function getScreenHeight()
{
	if (screen.height)
	{
		return screen.height;
	}	
	return 0;
};

function getScreenWidth()
{
	if (screen.width)
	{
		return parseInt(screen.width*0.9);
	}	
	return 0;
};


$.fn.checkFields = function(){
	var formName=$(this).attr("id");
        var $this=$(this);
	switch(formName){
            case "frmAdminLogin":
            var vl = new DP.validateForm();
                vl.valSetting = {fields : [
                    {id : "adminUsername", val : "", msg : "Username or Email", type : ""},
                    {id : "adminPassword", val : "", msg : "Password", type : ""}				
                    ]
                };	  
            return vl.runCheck(formName);
            break;
            
            case "frmAddEditBasicSettings":
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_type", val : 0, msg : "Type", type : "number"}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            default:
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "context", val : "", msg : "Please specify a purpose of this form", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            case "frmAddEditStaff":    
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_firstname", val : "", msg : "First Name", type : ""},
                    {id : "aEOE_lastname", val : "", msg : "Last Name", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            case "frmAddEditEvent":
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_edate", val : "", msg : "Event Date", type : ""},
                    {id : "aEOE_name_en", val : "", msg : "Event Name (EN)", type : ""},
                    {id : "aEOE_name_lv", val : "", msg : "Event Name (LV)", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            case "frmAddEditRoomType":
            case "frmAddEditPublication":    
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_name_en", val : "", msg : "Event Name (EN)", type : ""},
                    {id : "aEOE_name_lv", val : "", msg : "Event Name (LV)", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            
            case "frmAddEditBanner":
            case "frmAddEditGallery":      
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_image", val : "", msg : "Banner Image", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            
            case "frmAddEditMarketingBox":    
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_caption", val : "", msg : "Large Caption", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            case "frmAddEditDonation":
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_title", val : "", msg : "Salution", type : ""},
                    {id : "aEOE_firstname", val : "", msg : "First Name", type : ""},
                    {id : "aEOE_lastname", val : "", msg : "Last Name", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
            
            case "frmAddEditAdminUser":    
            var vl = new DP.validateForm();
            vl.valSetting = {fields : [
                    {id : "aEOE_name", val : "", msg : "-", type : ""}
                    ]
            };	  
            return vl.runCheck(formName);
            break;
	}
}

var DP = (typeof DP == "object") ? DP : {};

DP.validateForm = function(){
	
	
	
	

    //generic check value method
	var formValidated = function(whatForm)
	{	
			if(typeof(whatForm)!="undefined"){
				isfrmAddEditUserSubmit=true;
				 whatForm.submit();	
				 return true;
			}
	}
	
	
    var fromReset = function(elmId, wrongValue, messageText){
        //reset
        $(".from_wrp input").css({"border":"1px solid #ACA69F"});
        $(".from_wrp select").css({"border":"1px solid #ACA69F"});
        $("#error_messages").empty("");
    }

    //generic check value method
    var valueCheck = function(elmId, wrongValue, messageText){
        if($("[name='" + elmId + "']").val() == wrongValue){
            createAlert(elmId, messageText);
			return false;
		}
			removeAlert(elmId);
			return true;
				
    }
    
    //alert method
    var createAlert = function(elmId, messageText){
		elmId.addClass("missingField");
        stringAlert +="<p>" + elmId.closest("div").find("label").text() + "</p>";
    }
	var removeAlert = function(elmId){
		elmId.removeClass("missingField");
    }

    //zip validation
    var isZip = function(s){
        var reZip = new RegExp(/(^\d{5}$)|(^\d{5}-\d{4}$)/);
        if (!reZip.test(s)) {
            return false;
        }
        return true;
    }
    
    //checks if value is integer
    var isInt = function(n){
        var reInt = new RegExp(/^\d+$/);
        if (!reInt.test(n)) {
            return false;
        }
        return true;
    }
    
    //checks if value is pin
    var isPin = function(n){
        var rePin = new RegExp(/^\w{4,8}$/);
        if (!rePin.test(n)) {
            return false;
        }
        return true;
    }
    
    //checks if value is pin2
    var isPin2 = function(n){
        var rePin2 = new RegExp(/^\w{8,24}$/);
        if (!rePin2.test(n)) {
            return false;
        }
        return true;
    }
	//checks if value is integer
    var isPrice = function(n){
        var rePrice = new RegExp(/^\d+($|\,\d{3}($|\.\d{1,2}$)|\.\d{1,2}$)/);
        if (!rePrice.test(n)) {
            return false;
        }
        return true;
    } 
	
	//mail validation
    var isMail = function(s, elmId){
        var reMail = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        if (!reMail.test(s)) {
            return false;
        }		
        return true;
    }
    
    	//checks if value is password
    var isPassword = function(n){
        var rePassword = new RegExp(/^[\w!!?]{6,18}$/);
        if (!rePassword.test(n)) {
            return false;
        }
        return true;
    } 
    
    
    //public method checks fieds
    //requires 'valSetting' setting object
	
    this.runCheck = function(whatForm){
        //reseet form		
        //run checks
		var countTrueFilled=0;
		
		stringAlert="<h3>Required fields</h3>";
        for (i=0;i<this.valSetting.fields.length;i++){
			var fName=this.valSetting.fields[i].id;
			var fVal=this.valSetting.fields[i].val;
			var fieldName=$("#"+whatForm).find("[name='" + this.valSetting.fields[i].id + "']");
                        var fMessage=this.valSetting.fields[i].msg==""?fieldName.closest("div").find("label").text():this.valSetting.fields[i].msg;
            
            if(this.valSetting.fields[i].type == "zip"){
                //zip check
                if(isZip(fieldName.val()) == false){    
                    createAlert(fieldName, this.valSetting.fields[i].msg);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }
            else if (this.valSetting.fields[i].type == "number"){
                //checks for number
                if(isInt(fieldName.val()) == false || fieldName.val()==fVal){    
                    createAlert(fieldName, fMessage);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }
			else if (this.valSetting.fields[i].type == "price"){
                //checks for number
                if(isPrice(fieldName.val()) == false){    
                    createAlert(fieldName, fMessage);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }else if (this.valSetting.fields[i].type == "pin"){
                //checks for number
                if(isPin(fieldName.val()) == false){    
                    createAlert(fieldName, fMessage);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }else if (this.valSetting.fields[i].type == "pin2"){
                //checks for number
                if(isPin2(fieldName.val()) == false){    
                    createAlert(fieldName, fMessage);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }else if (this.valSetting.fields[i].type == "password"){
                //checks for number
                if(isPassword(fieldName.val(), fName) === false){ 
                    createAlert(fieldName, fMessage);
                }
				else{
                                    if(fName=='aEOE_passwordagain'){
                                        if(fieldName.val()!=$("input[name='aEOE_password']").val()) createAlert(fieldName, "Passwords must match.");
                                        else{
                                           removeAlert(fieldName);
                                            countTrueFilled++; 
                                        }
                                    }else{
                                        removeAlert(fieldName);
                                        countTrueFilled++;
                                    }
					
				}
            }
			else if (this.valSetting.fields[i].type == "email"){
                //checks for number
                if(isMail(fieldName.val(), fName) == false){    
                    createAlert(fieldName, fMessage);
                }
				else{
					removeAlert(fieldName);
					countTrueFilled++;
				}
            }
            else{
                //checks for value
                if(fieldName.val()==fVal){
                    createAlert(fieldName, fMessage);
                }else{
                    removeAlert(fieldName);
                    countTrueFilled++;
		}
            }
        }
		if(countTrueFilled>=this.valSetting.fields.length)
		{
			switch(whatForm){
				default:
                                    if(isAjaxLoaded) return false;
                                    isAjaxLoaded=true;
                                    var postData=$("#"+whatForm).serialize();

                                    $.ajax({
                                       type: "POST",
                                       url: $("#"+whatForm).attr("action"),
                                       data: postData,
                                       dataType: "json",
                                       success: function(data){
                                           isAjaxLoaded=false;
                                               if(data["success"]==1){
                                                    if(whatForm=="frmAdminLogin"){
                                                        window.location.href=(data["redirect"]);
                                                    }else{
                                                        if(whatForm=="frmAddEditBasicSettings" || whatForm=="frmAddEditBasicContents"){
                                                            displayInfo(data["content"], $("#"+whatForm).closest("div"));
                                                        }else if(whatForm=="frmAddEditBiography"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listBiographies div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listBiographies div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditProduct"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listProducts div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listProducts div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditCustomer"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listCustomers div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listCustomers div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditTimeSheetForScheduler"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listTimesheets div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listTimesheets div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditPublication"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listPublications div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listPublications div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditTrainingUpdate"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listTrainingUpdates div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listTrainingUpdates div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditBanner"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listBanners div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listBanners div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditGallery"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listGalleries").prepend(data["content"]);
                                                            }else{
                                                                $("div#listGalleries div.item[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditMarketingBox"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listMarketingBoxes div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listMarketingBoxes div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditDonation"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listDonations div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listDonations div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }else if(whatForm=="frmAddEditAdminUser"){
                                                            hide_overlay();
                                                            if(data["editid"]==0){
                                                                $("div#listAdminUsers div.tr:first").after(data["content"]);
                                                            }else{
                                                                $("div#listAdminUsers div.tr[data-id='"+data["editid"]+"']").replaceWith($(data["content"]));
                                                            }
                                                        }
                                                    }
                                               }
                                               else displayAlert(data["message"], $("body"));
                                            return false;
                                       }, error: function(){
                                           isAjaxLoaded=false;
                                       }
                                    });
				break;
                                
			}
			
		}
		else
		{
			displayAlert(stringAlert, $("#"+whatForm).closest("div"));
			return false;
			return false;
		}
		
    }
	
	
}

$.fn.limitMaxChar=function(c){
    return this.each(function(){
    var $this=$(this);
    var set=typeof(c)!="undefined"?c:parseInt($(this).attr("maxlength"));
    var remain;
    var tlength;
    var tval = $this.val();
    tlength = tval.length;
    remain = parseInt(set - tlength);
    $("span[rel='" + $this.attr("name") + "']").text(remain);
    $this.keypress(function(e) {
        tval = $this.val();
        tlength = tval.length;
        remain = parseInt(set - tlength);
    $("span[rel='" + $(this).attr("name") + "']").text(remain);
    if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
        $this.val((tval).substring(0, tlength - 1));
    }
});
    });
};

$.fn.reSortSortings=function(c, tbl){
    var tbl=typeof(tbl)!="undefined"?tbl:"photos";
    var $this=$(this);
    var idString=""; 
    var reString=/\(\d+,\d+\)$/;
    var counter=0;
    $this.find(c).each(function(){
        var classOddEven=$(this).index()%2==0?"even":"odd";
       var a=(parseInt($(this).index()))*1000;
       $(this).attr("data-sorting", a).removeClass("even");
       $(this).attr("data-sorting", a).removeClass("odd");
       $(this).attr("data-sorting", a).addClass(classOddEven);
       if(counter>0){
           idString +=",";
       }
       idString +=$(this).attr("data-id")!=0?"(" + $(this).attr("data-id") + "," + a + ")":"";
       counter+=$(this).attr("data-id")!=0?1:0;
    });
    if(!reString.test(idString)){ $this.sortable("enable"); return false;}
    var postData={
        ids: idString,
        table: tbl,
        context: "reSortSortings"
    }    
    $.ajax({
        type: "POST",
        url: "php/ajaxDispatcher.php",
        data: postData,
        dataType: "json",
        success: function(data){
                if(data["success"]==1){
                    $this.sortable("enable");
                }else{
                    displayAlert(data["message"], $("body"));
                }
        }
    });
};

$.fn.wrapAjaxContent=function(){
    return this.each(function(){
        var $this=$(this);
        var postData={context: $this.attr("data-context")};
        postData['section']=$("div#main").attr("data-section");
        if($this.attr("data-id")) postData['id']=$this.attr("data-id");
        if($this.attr("data-page")) postData['page']=$this.attr("data-page");
        $("div[data-content='wrapajaxcontent']", $this).html(ajaxLoader);
        $.ajax({
            type: "POST",
            url: pathToAD,
            data: postData,
            dataType: "json",
            success: function(data){
                ajaxLoaded=false;
                if(data["success"]==1){
                    $("div[data-content='wrapajaxcontent']", $this).append(data["content"]);
                    if(data["context"]=="wrapHomeDashboard"){
                        $("div#wrapGeneralSiteStatistic li").addaptPositions("div.bodytext", "p");
                        $("div#wrapGeneralSiteStatistic li").addaptPositions("div.circlestat", "span");
                    }
                    window.setTimeout(function(){
                       $("div[data-content='wrapajaxcontent']", $this).addClass("active"); 
                       $("div[data-content='wrapajaxcontent'] div.ajaxLoader", $this).fadeOut(1000, function(){$(this).remove();});
                    }, 500);
                }else{
                    displayAlert(data["message"], $this);
                }
            },
            error: function(){
                ajaxLoaded=false;
            }
         });
    });
};

$.fn.addaptPositions=function(a,b){
    return this.each(function(){
       var $this=$(this);
       var bHeight=parseInt($(a+" "+b, $this).outerHeight());
       $(a+" "+b, $this).css({marginTop: -1*(bHeight/2)+"px"});
    });
}

function scrollToAnchor(aid){
    if($("[data-anchor='"+ aid +"']").length<1) return false;
    var aTag = $("[data-anchor='"+ aid +"']");
    var aTagTop=parseInt(aTag.offset().top);
    var headerTop=parseInt($("header").outerHeight());
    var _scrollHeight=aTagTop-headerTop;
    $('html,body').animate({scrollTop: _scrollHeight},'slow');
}

function deleteMedia(a,b,c,d,form, f){
    var postData={
        "photos": a,
        "field": f,
        "id": c,
        "table": d,
        "context": "deleteMedia"
    };
    
     $.ajax({
        type: "POST",
        url: "php/ajaxDispatcher.php",
        data: postData,
        dataType: "json",
        success: function(data){
                if(data['success']==1){
                    b.fadeOut("slow", function(){
                        $(this).remove();
                        if($("input[name='aEOE_"+f+"']", form).length>0) $("input[name='aEOE_"+f+"']", form).val("");
                    });
                }
        }
     });
}

function deletePhoto(a,b,c,d,form, f){
    var postData={
        "photos": a,
        "field": f,
        "id": c,
        "table": d,
        "context": "deletePhoto"
    };
    
     $.ajax({
        type: "POST",
        url: "php/ajaxDispatcher.php",
        data: postData,
        dataType: "json",
        success: function(data){
                if(data['success']==1){
                    b.fadeOut("slow", function(){
                        $(this).remove();
                        if($("input[name='aEOE_"+f+"']", form).length>0) $("input[name='aEOE_"+f+"']", form).val("");
                    });
                }
        }
     });
}

function displayCPhoto(photo, obj){
    obj.prepend(photo);
}

function displayAlert(a, b){
    var _fadeIn=300;
    var c="<div class='closeOverlay abs right0'><a class='relative' href=''>x</a></div><div class='errContent relative'>" + a + "</div>";
    var d=$("<div class='errMessage1 abs' />"); 
    b.prepend(d);
    //d.html(c).fadeIn("fast", function(){$(this).delay(4000).fadeOut("fast", function(){$(this).remove();});});
    d.html(c);
    scrollToAnchor("content");
    d.fadeIn(_fadeIn, function(){
        $("div.closeOverlay a, a", this).click(function(e){
            e.preventDefault();
            $("div.overlayDisabler", b).remove();
            d.fadeOut(_fadeIn, function(){
                $(this).remove();
            });
        });
    });
    $(document).mouseup(function(){
        $("div.overlayDisabler", b).remove();
            d.remove();
    });
}

function displayInfo(a, b){
    var _fadeIn=300;
    var c="<div class='closeOverlay abs right0'><a class='relative' href=''>x</a></div><div class='infoContent relative'>" + a + "</div>";
    var d=$("<div class='errMessage1 abs' />"); 
    b.prepend(d);
    //d.html(c).fadeIn("fast", function(){$(this).delay(4000).fadeOut("fast", function(){$(this).remove();});});
    d.html(c);
    d.fadeIn(_fadeIn, function(){
        $("div.closeOverlay a, a", this).click(function(e){
            e.preventDefault();
            $("div.overlayDisabler", b).remove();
            d.fadeOut(_fadeIn, function(){
                $(this).remove();
            });
        });
    });
    $(document).mouseup(function(){
        $("div.overlayDisabler", b).remove();
            d.remove();
    });
}

function show_overlay(responseText) {
    if ($("div#overlay").length==0){
        append_overlay(responseText);
        overlay_wrapper.fadeIn("fast");
    }
    else{
        if($("div#overlay div.popup.has-children").length>0){
           if($("div#overlay div.popup.has-children > div.insideContent > div.popup").length<1){
                $("div#overlay div.popup.has-children > div.insideContent").addClass("passive").prepend(responseText);
            }
        }else{
            $("div#overlay").html(responseText);
        }
        attach_overlay_events();
    } 
    if(parseInt(getBrowserWidth())-740<=0){
        
    }else{
        $("body").css({overflow: "hidden"});
        $("#overlay").css({overflow: "auto", height: "100%"});
    }
    
}

function hide_overlay() {
    $("div#overlay div.popup:last").fadeOut(300, function(){
        $(this).remove();
            if($("div#overlay div.popup:last").length<1){
                overlay_wrapper.fadeOut(300, function(){
                    $(function(){
                        var url = window.location.toString();
                        url = url.split("#")[0];
                        window.history.pushState('Object', 'Title', url);
                    });
                    overlay_wrapper.remove();
                    $("body").css({overflow: "auto"});
                });
            }else{
                $("div#overlay div.popup:last > div.insideContent").removeClass("passive");
            }
        });
}

function append_overlay(responseText) {
    if(parseInt(getBrowserWidth())-740<=0){
        overlay_wrapper = $('<div id="overlay"></div>').prependTo( $('div#main') );
    }else{
    overlay_wrapper = $('<div id="overlay"></div>').appendTo( $('BODY') );
    }
	overlay_wrapper.html(responseText);
	
    attach_overlay_events();
}

function attach_overlay_events() {
    if($("div.pautoclose").length>0){
            setTimeout(function() {hide_overlay();}, 4000);
    }
    window.setTimeout(function(){
        var curPopupHeight=parseInt($("#overlay div.popup").outerHeight());
        var curBrowserHeight=parseInt(getBrowserHeight());
        if(curPopupHeight-100-curBrowserHeight>=0){
            $("#overlay div.popup").removeClass("top50").css({top: 50+"px"});
        }
    }, 200);
    

    $('div#overlay .closeOverlay').click( function(ev) {
        ev.preventDefault();
        hide_overlay();		
    });
    
    $(document).on("click", "[data-action='hideoverlay']", function(e){
       e.preventDefault();
       hide_overlay();
    });
    
}

$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = this;
    });
}

function handleWithLoginBlock(){
    $("#frmAdminLogin").submit(function(e){
        e.preventDefault();
        return $(this).checkFields();
        return false;
    });
}

function handleWithTableListOuter(div){
    if($("div.fileUploaderContainer div.newfileUploader input.mf-file", div).length>0){
       $("div.fileUploaderContainer", div).each(function(){
            var countUploaderRows=$(this).index();
            var _e;
            if($(this).attr("data-infunction")) _e=$(this).attr("data-infunction");
            else _e=_c;
            wrapVideoUploadersByFields(div, $("div.newfileUploader input.mf-file", this), countUploaderRows, _e);
       });
     
    }
}

function handleWithTableList(div){
    
    div.on( "click", "a[data-action='addedititem']",function(e){
        e.preventDefault();
        var $this=$(this);
        var postData={id: $this.attr("data-id"), context: $this.attr("data-context")};
        if($this.attr("data-eventid")){
            postData["eventid"]=$this.attr("data-eventid");
        }
        if($this.attr("data-section")){
            postData["section"]=$this.attr("data-section");
        }
        if($this.closest("#frmAddEditSchedulerDetails").length>0){
                postData["schedulerid"]=$this.closest("#frmAddEditSchedulerDetails").find("input[name='aEOE_id']").val();
            }
        if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        $.ajax({
               type: "POST",
               url: "php/ajaxDispatcher.php",
               data: postData,
               dataType: "json",
               success: function(data){
                   isAjaxLoaded=false;
                   if(data["success"]==1){ 
                      show_overlay(data["content"]);
                      window.setTimeout(function(){
                          handleWithEditForms($("#overlay div.popup form:first"));
                      }, 200);
                   }else{
                       displayAlert(data["message"], $this.closest("div"));
                   }
               },error: function(){
                   isAjaxLoaded=false;
               }
        });
    });
    
    div.on( "click", "a[data-action='markonline']",function(e){
        e.preventDefault();
        var $this=$(this);
        var postData={id: $this.attr("data-id"), context: $this.attr("data-context")};
        if($this.attr("data-eventid")){
            postData["eventid"]=$this.attr("data-eventid");
        }
        if($this.attr("data-section")){
            postData["section"]=$this.attr("data-section");
        }
        if($this.closest("#frmAddEditSchedulerDetails").length>0){
                postData["schedulerid"]=$this.closest("#frmAddEditSchedulerDetails").find("input[name='aEOE_id']").val();
            }
        if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        $.ajax({
               type: "POST",
               url: "php/ajaxDispatcher.php",
               data: postData,
               dataType: "json",
               success: function(data){
                   isAjaxLoaded=false;
                   if(data["success"]==1){ 
                      $this.html($(data["content"]));
                   }else{
                       displayAlert(data["message"], $("body"));
                   }
               },error: function(){
                   isAjaxLoaded=false;
               }
        });
    });
    
    div.on( "click", "[data-action='exportitem']",function(e){
        e.preventDefault();
        var $this=$(this);
        window.location.href=$this.attr("data-link");
        
    });
    
    
    div.on( "click", "a[data-action='deleteitem']",function(e){
        e.preventDefault();
        var $this=$(this);
        
        var curRowTHCloned,curRowCloned=null;
        curRowTHCloned=$this.attr("data-title");
        curRowCloned=templateButtons;
        
        var curRow=templatePopup;
        show_overlay(curRow);
        if($this.attr("data-alttitle")){
             $("div#overlay div.popup #extraReplaceIntroText").html($this.attr("data-alttitle"));
        }
        $("div#overlay div.popup div[data-type='content-replace']").empty();
        $("div#overlay div.popup div[data-type='content-replace']").append(curRowTHCloned);
        $("div#overlay div.popup div[data-type='content-replace']").append(curRowCloned);
        
        window.setTimeout(function(){
            $("div#overlay div.popup div[data-type='content-replace'] button[data-action='cancel']").click(function(){
                hide_overlay(); 
            });
            var yesBtn=$("div#overlay div.popup div[data-type='content-replace'] button[data-action='decide']");
            yesBtn.attr({
                "data-context": $this.attr("data-context"),
                "data-id": $this.attr("data-id")
            });
            if($this.closest("#frmAddEditSchedulerDetails").length>0){
                yesBtn.attr({"data-schedulerid": $this.closest("#frmAddEditSchedulerDetails").find("input[name='aEOE_id']").val()});
            }
            yesBtn.click(function(){
                var $this2=$(this);
                var postData={id: $this2.attr("data-id"), context: $this2.attr("data-context")};
                if($this2.attr("data-schedulerid")){
                    postData["schedulerid"]=$this2.attr("data-schedulerid");
                }
                if(isAjaxLoaded) return false;
                isAjaxLoaded=true;
                $.ajax({
                       type: "POST",
                       url: "php/ajaxDispatcher.php",
                       data: postData,
                       dataType: "json",
                       success: function(data){
                           isAjaxLoaded=false;
                           if(data["success"]==1){ 
                               hide_overlay();
                               if($this.attr("data-context")=="truncateTimeSheetForScheduler"){
                                   $this.closest("div.tr").removeClass("bg-green").find("div.td[data-rel='clearafter']").empty();
                                   return false;
                               }
                               $this.closest("div.tr").fadeOut("fast", function(){
                                   $(this).remove();
                             $("div.tr", div).each(function(){
                                var classOddEven=$(this).index()%2==0?"even":"odd";
                               $(this).removeClass("even");
                               $(this).removeClass("odd");
                               $(this).addClass(classOddEven); 
                             });
                           });
                           }else{
                               displayAlert(data["message"], yesBtn.closest("div[data-type='content-replace']"));
                           }
                       },error: function(){
                           isAjaxLoaded=false;
                       }
                });
            });
        }, 500);
       
       
       
    });
    
    
    
   div.sortable({ 
        items: "div.sortableRow",
        axis: "y",           
        update: function(event, ui) {
            if(parseInt(ui.item.attr("data-id"))>0){
                div.sortable( "disable" ); 
                div.reSortSortings("div.sortableRow", div.attr("data-name"));
            }
        }
        });
    
    $("input.mf-checkbox", div).click(function(){
        var $this=$(this);
        var $val;
        if($(this).is(":checked")) $val=1;
        else $val=0;
        var postData={id: $this.attr("data-id"), what: $val, context: $this.attr("data-context")};
       if(isAjaxLoaded) return false;
            isAjaxLoaded=true;
            $.ajax({
		   type: "POST",
		   url: "php/ajaxDispatcher.php",
		   data: postData,
		   dataType: "json",
		   success: function(data){
                       isAjaxLoaded=false;
                       if(data["success"]==1){
                           switch($val){
                               case 1:
                               $this.closest("div.tr").addClass("highlighted");    
                               break;
                               case 0:
                               $this.closest("div.tr").removeClass("highlighted");    
                               break;
                           }
                           
                       }
                   }
            });
       
    });
    
}

function wrapVideoUploadersByFields(form, obj, i, infunction){
    var _resize='standard';
    var files=[];
    var actionTarget=form.attr("action");
    var context="uploadPhoto";
    if(infunction=="eventimportattendees"){
        context="importEventAttendees";
    }
    var whatUploaderID=obj.closest("div.fRow");
    if(whatUploaderID.attr("data-infunction")) infunction=whatUploaderID.attr("data-infunction");
    var postData="/?context="+context+"&pathToUpload=images&resize="+_resize+"&ID="+whatUploaderID.attr("data-id")+"&infunction="+infunction+"&table="+whatUploaderID.attr("data-table");
    if(whatUploaderID.attr("data-year")) postData +="&year=" + whatUploaderID.attr("data-year");
    if(whatUploaderID.attr("data-action")) actionTarget=whatUploaderID.attr("data-action");
    obj.fileupload({
        url: actionTarget + postData,
        dataType: "json",
        singleFileUploads: true,
        autoupload: true,
        limitMultiFileUploads: 5,
        multipart: false,
        limitMultiFileUploadSize: 209715200,
        contentType: "application/octet-stream",
        dropZone: whatUploaderID.find("div.qq-drag-and-drop"),
        formData: {},
        add: function(e, data) {
            avoidDefaultActionOnClose="cancelVideoUpload";
            var uploadErrors = [];
            var acceptFileTypes = /(\.|\/)(jpe?g|JPE?G|gif|png|PNG')$/i;
            if(infunction=="importcsv"){
                acceptFileTypes = /(\.|\/)(csv|CSV')$/i;
                $("[data-rel='iffalse']", form).slideUp("fast");
                $("[data-rel='iftrue']", form).slideDown("fast");
            }
            else if(infunction=="worddoc"){
                acceptFileTypes = /(\.|\/)(doc|DOC|docx|DOCX')$/i;
            }else if(infunction=="powerpoint"){
                acceptFileTypes = /(\.|\/)(ppt|PPT|pptx|PPTX')$/i;
            }else if(infunction=="brochureurl"){
                acceptFileTypes = /(\.|\/)(pdf|PDF|doc|DOC|docx|DOCX|jpe?g|JPE?G|gif|png|PNG')$/i;
            }else{
                acceptFileTypes = /(\.|\/)(jpe?g|JPE?G|gif|png|PNG')$/i;
            }
            $.each(data.files, function (index, file) {
                if(!acceptFileTypes.test(file.name)){
                    uploadErrors.push('This kind of file is not supported');
                }else{
                    files.push(file.name);
                }
            });
            if(uploadErrors.length > 0) {
                    alert(uploadErrors.join("\n"));
                } else {
                    var jqXHR = data.submit();
                }
            $("div.progressBarHolder span.cancelUpload a", whatUploaderID).click(function(e){
                e.preventDefault();
                jqXHR.abort();
                return false;
            });
            $('div#dragAndDropArea div.closeBtn a').click( function(e) {
                e.preventDefault();
                if(avoidDefaultActionOnClose=="cancelVideoUpload"){
                    jqXHR.abort();
                }
            });
        },
        drop: function (e, data) {
            avoidDefaultActionOnClose="cancelVideoUpload";
            whatUploaderID.addClass("uploadInProgress");
            var _counter=1;
            var curItem=[];
            $.each(data.files, function (index, file) {
                    $(".fileUploaderContainer .overlayMove").append("<div class='progressBarHolder activated' data-index='"+file.name+"'><span class='uploadedFileText abs'>UPLOAD: " + file.name + "</span><span class='progressBarText abs'></span><span class='cancelUpload abs'><a href='#'>prekini upload</a></span><div class='progressBar'></div></div>");
                });
        },
        change: function (e, data) {
            avoidDefaultActionOnClose="cancelVideoUpload";
           
            whatUploaderID.addClass("uploadInProgress");
            var _counter=1;
            var curItem=[];
            $.each(data.files, function (index, file) {
                    $(".fileUploaderContainer .overlayMove").append("<div class='progressBarHolder activated' data-index='"+file.name+"'><span class='uploadedFileText abs'>UPLOAD: " + file.name + "</span><span class='progressBarText abs'></span><span class='cancelUpload abs'><a href='#'>prekini upload</a></span><div class='progressBar'></div></div>");
                     
                });
        },
        progress: function (e, data) {
            
                var progress = parseInt(data.loaded / data.total * 100, 10);
                
                $("div.progressBarHolder[data-index='"+data.files[0].name+"'] div.progressBar", whatUploaderID).css({width: progress+"%"});
                $("div.progressBarHolder[data-index='"+data.files[0].name+"'] span.progressBarText", whatUploaderID).text(progress+"%");
                
        },
        
        done: function (e, data) {
            var responseJSON=data.result;
            if(responseJSON['success']!==false){
                whatUploaderID.removeClass("uploadInProgress").addClass("uploadCompeted");
                whatUploaderID.closest("div#fileUploaderContainer").find("div#uploadedFilesHere").find("ul:first").find("li.hideIfFilesUploaded").addClass("hidden");
                $("div.progressBarHolder[data-index='"+data.files[0].name+"']", whatUploaderID).fadeOut("slow", function(){$(this).remove();});
                if(whatUploaderID.find("input[name^='aEOE_image']").length>0){
                    whatUploaderID.find("input[name^='aEOE_image']").val(responseJSON["filename"]);
                }
                if(whatUploaderID.find("input[name^='aEOE_videothumbnail']").length>0){
                    whatUploaderID.find("input[name^='aEOE_videothumbnail']").val(responseJSON["filename"]);
                }
                if(whatUploaderID.find("input[name^='aEOE_logo']").length>0){
                    whatUploaderID.find("input[name^='aEOE_logo']").val(responseJSON["filename"]);
                }
                
                
                switch(infunction){
                    default:
                    case null:
                    whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail"]);
                    if(whatUploaderID.find("input[name^='aEOE_media']").length>0){
                            whatUploaderID.find("input[name^='aEOE_media']").val(responseJSON["filename"]);
                        }
                    break;
                    case "eventLocations":
                    $("div.gMap", form).html(responseJSON["thumbnail"]);
                    $("#fileUploaderContainer", whatUploaderID).html("<button type='button' class='mf-button redish wide' data-action='removegmap'>REMOVE CUSTOM MAP</button>");
                    break;
                    case "eventimportattendees":
                        window.location.reload();
                    break;
                    case "worddoc": 
                    case "powerpoint":    
                        whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail"]);
                        if(whatUploaderID.find("input[name^='aEOE_media']").length>0){
                            whatUploaderID.find("input[name^='aEOE_media']").val(responseJSON["filename"]);
                        }
                    break;
                    case "eventadditional":
                        whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail"]);
                        if(whatUploaderID.find("input[name^='aEOE_additional']").length>0){
                            whatUploaderID.find("input[name^='aEOE_additional']").val(responseJSON["filename"]);
                        }
                    break;
                    case "eventemplatesettings":
                        whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail"]);
                        if(whatUploaderID.find("input[name='aEOE_bgimage']").length>0){
                            whatUploaderID.find("input[name='aEOE_bgimage']").val(responseJSON["filename"]);
                        }
                        if(whatUploaderID.find("input[name='aEOE_bannerimage']").length>0){
                            whatUploaderID.find("input[name='aEOE_bannerimage']").val(responseJSON["filename"]);
                        }
                    break;
                    case "importcsv":
                        window.location.reload();
                    break;
                    case "brochureurl":
                        whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail"]);
                        if(whatUploaderID.find("input[name^='aEOE_brochureurl']").length>0){
                            whatUploaderID.find("input[name^='aEOE_brochureurl']").val(responseJSON["filename"]);
                        }
                         if(whatUploaderID.find("input[name^='aEOE_media']").length>0){
                            whatUploaderID.find("input[name^='aEOE_media']").val(responseJSON["filename"]);
                        }
                    break;
                    case "gallery":
                    $("#listGalleries").prepend($(responseJSON["thumbnail"]));
                    whatUploaderID.find("div.mAOLThumbnail").html(responseJSON["thumbnail1"]);
                    break;
                }
                
            }else{
                switch(infunction){
                    default:
                    case null:
                        
                    break;
                    case "eventimportattendees":
                    $("[data-rel='iftrue']", form).slideUp("fast");
                    $("[data-rel='iffalse']", form).slideDown("fast");
                    break;
                }
            }
        } 
    });
   
}

function handleWithEditForms(form){
    
    if($("textarea.tinymce").length>0){
        tinymce.init({
            selector:'textarea.tinymce',
            theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools'
  ],
  style_formats_merge: true,
  style_formats: [
      {title: 'Special', items: [
        {title: 'Important', inline: 'span',  classes: 'important'},
        {title: 'Left Block', block: 'div', wrapper: true, classes: 'col-md-6 left'},
        {title: 'Right Block', block: 'div', wrapper: true, classes: 'col-md-6 redbg right'},
        {title: 'Gray Block', block: 'div', wrapper: true, classes: 'grayblock'}
      ]}
  ],
  toolbar1: 'insertfile undo redo | styleselect | fontselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image paste',
  toolbar2: 'print preview media | forecolor backcolor emoticons | code',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '../../assets/css/bootstrap.css',
    'css/rte.css'
  ],
  relative_urls: false,
  paste_data_images: true,
  images_upload_handler: function (blobInfo, success, failure) {
    var xhr, formData;
    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', 'postAcceptor.php');
    xhr.onload = function() {
      var json;

      if (xhr.status != 200) {
        failure('HTTP Error: ' + xhr.status);
        return;
      }
      json = JSON.parse(xhr.responseText);

      if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
      }
      success(json.location);
    };
    formData = new FormData();
    formData.append('file', blobInfo.blob(), fileName(blobInfo));
    xhr.send(formData);
  }
        });
    }
    
    var _c=arguments[1]?arguments[1]:null;
    var _d=arguments[2]?arguments[2]:null;
    
    if(form.attr("id")=="frmAddEditBasicSettings"){
        $("input[name='aEEE_inmenu']", form).click(function(){
            var $this=$(this);
            var inmenu=$this.is(":checked")?1:0;
            if(isAjaxLoaded) return false;
                isAjaxLoaded=true;
                var postData={
                "id": $this.attr("data-id"),
                "inmenu": inmenu,
                "context": "markPageOnline"
            }

            $.ajax({
               type: "POST",
               url: form.attr("action"),
               data: postData,
               dataType: "json",
               success: function(data){
                   isAjaxLoaded=false;
                   if(data["success"]==1){
                       if(inmenu==1){
                           $this.closest("div.fRow").addClass("isOnline");
                       }else{
                           $this.closest("div.fRow").removeClass("isOnline");
                       }
                   }else{
                       displayAlert(data["message"], form.closest("div"));
                   }                   
               }
            });
        });
    }
    
    if(form.attr("id")=="frmAddEditSchedulerDetails"){
        handleWithTableList($("div.table[data-action='handletable']", form));
    }
    
    if(form.attr("id")=="frmAddEditEvent"){
        $("textarea[name^='aEOE_description']", form).limitMaxChar();
    }
    
    $(".limitMaxChar", form).limitMaxChar();
    
    if(form.attr("id")=="frmAddEditDonation"){
        $("select[name='aEOE_ispaypal']", form).change(function(){
           $("div.fRow[data-rel='"+$(this).attr("name")+"']", form).addClass("hidden").find("input.mf-input").val("");
           $("div.fRow[data-rel='"+$(this).attr("name")+"'][data-value='"+$(this).val()+"']", form).removeClass("hidden");
        });
    }
    
    if(form.attr("id")=="frmAddEditPublication"){
        $("select[name='aEOE_publicationsource']", form).change(function(){
           $("div.fRow[data-rel='"+$(this).attr("name")+"']", form).addClass("hidden").find("input.mf-input").val("");
           $("div.fRow[data-rel='"+$(this).attr("name")+"'][data-value='"+$(this).val()+"']", form).removeClass("hidden");
        });
    }
    
    if(form.attr("id")=="frmAddEditBanner"){
        $("select[name='aEOE_isbutton']", form).change(function(){
           $("div.fRow[data-rel='"+$(this).attr("name")+"']", form).addClass("hidden").find("input.mf-input").val("");
           $("div.fRow[data-rel='"+$(this).attr("name")+"'][data-value='"+$(this).val()+"']", form).removeClass("hidden");
        });
    }
   
    if($("input.datepicker", form).length>0){
        $("input.datepicker", form).datepicker({
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 1000);
                }, 0);
            },
            dateFormat: "yy-mm-dd"
        });
    }
    
    if($("input[name='aEOE_edate']", form).length>0){
        $("input[name='aEOE_edate']", form).datepicker({
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 1000);
                }, 0);
            },
            dateFormat: "yy-mm-dd"
        });
    }
    
    if($("select[name='aEOE_country']", form).length>0){
        $("select[name='aEOE_country']", form).change(function(){
            if(isAjaxLoaded) return false;
            isAjaxLoaded=true;
            var postData={
            "id": $(this).val(),
            "context": "wrapProvinces"
        }

        $.ajax({
           type: "POST",
           url: form.attr("action"),
           data: postData,
           dataType: "json",
           success: function(data){
               isAjaxLoaded=false;
               if(data["success"]==1){
                   $("select[name='aEOE_province']", form).replaceWith(data["content"]);
               }else{
                   displayAlert(data["message"], form.closest("div"));
               }                   
           }
        });
        });
    }
    
    
    if($("div.fileUploaderContainer div.newfileUploader input.mf-file", form).length>0){
       $("div.fileUploaderContainer", form).each(function(){
            var countUploaderRows=$(this).index();
            var _e;
            if($(this).attr("data-infunction")) _e=$(this).attr("data-infunction");
            else _e=_c;
            wrapVideoUploadersByFields(form, $("div.newfileUploader input.mf-file", this), countUploaderRows, _e);
       });
     
    }
    
    form.on("click", "a.deletePhoto", function(e){
       e.preventDefault();
       var a=$(this).attr("data-photos");
       var c=$(this).closest("div.mAOLThumb");
       var d=$(this).attr("data-id");
       var t=$(this).attr("data-table");
       var f=$(this).attr("data-field");
       
       deletePhoto(a,c,d,t,form, f);
       return false;
    });
    
    form.on("click", "a.deleteMedia", function(e){
        e.preventDefault();
        var a=$(this).attr("data-photos");
        var c=$(this).closest("div.mAOLThumbnail");
        var d=$(this).attr("data-id");
        var t=$(this).attr("data-table");
        var f=$(this).attr("data-field");
        deleteMedia(a,c,d,t,form, f);
       return false;
    });
    
    form.submit(function(e){
       e.preventDefault();
       if($("textarea.tinymce", form).length>0) tinyMCE.triggerSave();
       return $(this).checkFields();
       return false;
   });   
}

function handleWithLogo(div){
    div.addClass("activated");
    
    $("#frmAdminLogin", div).submit(function(e){
        e.preventDefault();
        return $(this).checkFields();
        return false;
    });
}

function handleWithGalleriesTable(div){
    var _c;
    if($("div.fileUploaderContainer div.newfileUploader input.mf-file", div).length>0){
        var countUploaderRows=1;
        wrapVideoUploadersByFields(div, $("div.fileUploaderContainer div.newfileUploader input.mf-file", div), countUploaderRows, _c);
     }
     
     div.on("click", "a.deletePhoto", function(e){
       e.preventDefault();
       var a=$(this).attr("data-photos");
       var c=$(this).closest("div.item");
       var d=$(this).attr("data-id");
       var t=$(this).attr("data-table");
       var f=$(this).attr("data-field");
       
       deletePhoto(a,c,d,t,div, f);
       return false;
    });
    
    div.on("click", "a[data-action='addedititem']", function(e){
       e.preventDefault();
       var c=$(this).closest("div.item");
       c.addClass("editPhoto");
       e.preventDefault();
        var $this=$(this);
        var postData={id: $this.attr("data-id"), context: $this.attr("data-context")};
        if($this.attr("data-eventid")){
            postData["eventid"]=$this.attr("data-eventid");
        }
        if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        $.ajax({
               type: "POST",
               url: "php/ajaxDispatcher.php",
               data: postData,
               dataType: "json",
               success: function(data){
                   isAjaxLoaded=false;
                   if(data["success"]==1){ 
                      show_overlay(data["content"]);
                      window.setTimeout(function(){
                          handleWithEditForms($("#overlay div.popup form:first"));
                      }, 200);
                   }else{
                       displayAlert(data["message"], $this.closest("div"));
                   }
               },error: function(){
                   isAjaxLoaded=false;
               }
        });
    });
}

function handleWithEventTemplates(div){
    $("li[data-action='loadtemplateform'] item div.thumb", div).click(function(){
        var $this=$(this);
        $this.closest("ul").find("div.overblue").removeClass("activated");
        $this.closest("li").find("div.overblue").addClass("activated");
        var eventid=$this.closest("li").attr("data-eventid");
        var templateid=$this.closest("li").attr("data-templateid");
        var id=$this.closest("li").attr("data-id");
        var context=$this.closest("li").attr("data-context");
        
        //Add/edit template setting form and assign clicked template to the event
        if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        var postData={id: id, templateid: templateid, eventid: eventid, context: context};
            $.ajax({
            type: "POST",
            url: pathToAD,
            data: postData,
            dataType: "json",
            success: function(data){
                isAjaxLoaded=false;
                if(data["success"]==1){
                   $this.closest("li").attr("data-id", data["id"]); 
                   $("#wrapEditEventTemplateSettings", div).replaceWith(data["content"]);
                   window.setTimeout(function(){
                       handleWithEventTemplates(div);
                   }, 500);
                }else{
                    displayAlert(data["message"], $this.closest("div.regularForm"));
                }
           },
           error: function(){
               isAjaxLoaded=false;
           }
        });
    });
    
    $("li[data-action='loadtemplateform'] item a[data-action='viewbigphoto']", div).click(function(e){
        e.preventDefault();
        var $this=$(this);
        if($this.attr("data-photo")==""){
            displayAlert("<h3>Missing photo</h3><p>Click on this template to edit.</p>", $this.closest("li"));
            return false;
        }
        var curRow=templatePopup1;
        show_overlay(curRow);
        $("div#overlay div.popup div[data-type='content-replace']").empty();
        $("div#overlay div.popup div[data-type='content-replace']").html("<img src='"+$this.attr("data-photo")+"' alt='' />")
    });
    
    $("input.mf-colorpicker", div).colorpicker();
    
    
    
    handleWithEditForms($("#frmAddEditEventTemplate", div));
}


$(document).ready(function(){
    $("nav#menu li:has(ul)").click(function(){$(this).toggleClass("hoveratag");});
    
    if($("#logo").length>0){
        handleWithLogo($("#logo"));
    }
    
    $("a.displayLoginBlock").click(function(e){
            e.preventDefault();
            $("div#loginBlockContainer").toggle("blind", "", 300);
    });
    
    
    if($("[data-action='wrapajaxcontent']").length>0){
        return false;
        $("[data-action='wrapajaxcontent']").wrapAjaxContent();
    }
    
    if($("div#loginBlockContainer").length>0) handleWithLoginBlock();
    
    if($("#listCommitteeBoardMembers").length>0) handleWithTableList($("#listCommitteeBoardMembers"));
    if($("div.table[data-action='handletable']").length>0) handleWithTableList($("div.table[data-action='handletable']"));
    
    if($("[data-table='galleries']").length>0) handleWithGalleriesTable($("[data-table='galleries']"));
    
    if($("div#userListTable").length>0) handleWithTableListOuter($("div#userListTable"));
    
    if($("#frmAddEditBasicSettings").length>0){
        handleWithEditForms($("#frmAddEditBasicSettings"));
    }
    
    if($("#frmAddEditBasicContents").length>0){
        handleWithEditForms($("#frmAddEditBasicContents"));
    }
    
    $("a").tooltip();
   
});

String.prototype.times = function(n) {
    return Array.prototype.join.call(
            {length:n+1}, 
    this);
};