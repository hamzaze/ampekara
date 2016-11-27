Template7.registerHelper('stringify', function (context){
    var str = JSON.stringify(context);
    // Need to replace any single quotes in the data with the HTML char to avoid string being cut short
    return str.split("'").join('&#39;');
});

// Initialize your app
var myApp = new Framework7({
    material: true,
    precompileTemplates: true,
    template7Pages: true // need to set this
});



// Export selectors engine
var $$ = Dom7;

// Add view
var mainView = myApp.addView('.view-main', {
    // Because we use fixed-through navbar we can enable dynamic navbar
    dynamicNavbar: true
});

var isAjaxLoaded=false;  
var pathToAjaxDispatcher="http://e-solucije.com/am/php/ajaxDispatcher.php";


// Callbacks to run specific code for specific pages, for example for About page:
myApp.onPageInit('about', function (page) {
    // run createContentPage func after link was clicked
    $$('.create-page').on('click', function () {
        createContentPage();
    });
});

// Generate dynamic page
var dynamicPageIndex = 0;
function createContentPage() {
	mainView.router.loadContent(
        '<!-- Top Navbar-->' +
        '<div class="navbar">' +
        '  <div class="navbar-inner">' +
        '    <div class="left"><a href="#" class="back link"><i class="icon icon-back"></i><span>Back</span></a></div>' +
        '    <div class="center sliding">Dynamic Page ' + (++dynamicPageIndex) + '</div>' +
        '  </div>' +
        '</div>' +
        '<div class="pages">' +
        '  <!-- Page, data-page contains page name-->' +
        '  <div data-page="dynamic-pages" class="page">' +
        '    <!-- Scrollable page content-->' +
        '    <div class="page-content">' +
        '      <div class="content-block">' +
        '        <div class="content-block-inner">' +
        '          <p>Here is a dynamic page created on ' + new Date() + ' !</p>' +
        '          <p>Go <a href="#" class="back">back</a> or go to <a href="services.html">Services</a>.</p>' +
        '        </div>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>'
    );
	return;
}

$$.fn.checkFields = function(){
    var formName=$$(this).attr("id");
    var $this=$$(this);
    switch(formName){
        default:    
        var vl = new DP.validateForm();
        vl.valSetting = {fields : [
                {id : "context", val : "", msg : "What is this form for?", type : ""}
                ]
        };	  
        return vl.runCheck(formName);
        break;
    }
};

var DP = (typeof DP == "object") ? DP : {};

DP.validateForm = function(){
    //generic check value method
    var formValidated = function(whatForm){	
        if(typeof(whatForm)!="undefined"){
                isfrmAddEditUserSubmit=true;
                 whatForm.submit();	
                 return true;
        }
    };
	
    var fromReset = function(elmId, wrongValue, messageText){
        //reset
        $$(".from_wrp input").css({"border":"1px solid #ACA69F"});
        $$(".from_wrp select").css({"border":"1px solid #ACA69F"});
        $$("#error_messages").empty("");
    }

    //generic check value method
    var valueCheck = function(elmId, wrongValue, messageText){
        if($$("[name='" + elmId + "']").val() == wrongValue){
            createAlert(elmId, messageText);
			return false;
		}
			removeAlert(elmId);
			return true;
    };
    
    //alert method
    var createAlert = function(elmId, messageText){
		elmId.addClass("missingField");
        stringAlert +="<p>" + elmId.closest("div").find("label").text() + "</p>";
    };
    var removeAlert = function(elmId){
            elmId.removeClass("missingField");
    };

    //zip validation
    var isZip = function(s){
        var reZip = new RegExp(/(^\d{5}$)|(^\d{5}-\d{4}$)/);
        if (!reZip.test(s)) {
            return false;
        }
        return true;
    };
    
    //checks if value is integer
    var isInt = function(n){
        var reInt = new RegExp(/^\d+$/);
        if (!reInt.test(n)) {
            return false;
        }
        return true;
    };
    
    //checks if value is pin
    var isPin = function(n){
        var rePin = new RegExp(/^\w{4,8}$/);
        if (!rePin.test(n)) {
            return false;
        }
        return true;
    };
    
    //checks if value is pin2
    var isPin2 = function(n){
        var rePin2 = new RegExp(/^\w{8,24}$/);
        if (!rePin2.test(n)) {
            return false;
        }
        return true;
    };
	//checks if value is integer
    var isPrice = function(n){
        var rePrice = new RegExp(/^\d+($|\,\d{3}($|\.\d{1,2}$)|\.\d{1,2}$)/);
        if (!rePrice.test(n)) {
            return false;
        }
        return true;
    };
	
	//mail validation
    var isMail = function(s, elmId){
        var reMail = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        if (!reMail.test(s)) {
            return false;
        }		
        return true;
    };
    
    	//checks if value is password
    var isPassword = function(n){
        var rePassword = new RegExp(/^[\w!!?]{6,18}$/);
        if (!rePassword.test(n)) {
            return false;
        }
        return true;
    };
    
    
    //public method checks fieds
    //requires 'valSetting' setting object
	
    this.runCheck = function(whatForm){
        //reseet form		
        //run checks
		var countTrueFilled=0;
		
		stringAlert="<h3>Obavezna polja</h3>";
        for (i=0;i<this.valSetting.fields.length;i++){
			var fName=this.valSetting.fields[i].id;
			var fVal=this.valSetting.fields[i].val;
			var fieldName=$$("#"+whatForm).find("[name='" + this.valSetting.fields[i].id + "']");
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
                                        if(fieldName.val()!=$$("input[name='aEOE_password']").val()) createAlert(fieldName, "Passwords must match.");
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
                                    var postData=myApp.formToJSON("#"+whatForm);

                                    $$.ajax({
                                       type: "POST",
                                       url: $$("#"+whatForm).attr("action"),
                                       data: postData,
                                       dataType: "json",
                                       success: function(data){
                                           isAjaxLoaded=false;
                                               if(data["success"]==1){
                                                    if(whatForm=="frmLoginFEUser"){
                                                        switch(data['roles']){
                                                            case '3':
                                                            mainView.router.load({
                                                                template: Template7.templates.listTemplate,
                                                                context: data
                                                            });    
                                                            break;
                                                            case '2':
                                                            wrapDateModal();
                                                            /*
                                                            mainView.router.load({
                                                                template: Template7.templates.listProducts,
                                                                context: data
                                                            });  
                                                            */
                                                            break;
                                                        }
                                                    }else if(whatForm=="checkIsUserLoggedIn"){
                                                        switch(data['roles']){
                                                            case '3':
                                                            mainView.router.load({
                                                                template: Template7.templates.listTemplate,
                                                                context: data
                                                            });    
                                                            break;
                                                            case '2':
                                                            mainView.router.load({
                                                                template: Template7.templates.listProducts,
                                                                context: data
                                                            });    
                                                            break;
                                                        }
                                                        
                                                    }else if(whatForm=="frmProductOrders"){
                                                        resetForm($$("#"+whatForm));
                                                        $$.each(data['results'], function(key, value) {
                                                          $$("#"+whatForm+ " div.list-block div.item-content div.qty-current[data-id='"+key+"']").text(value["count"]);
                                                        });
                                                        displayInfo(data["message"], $$("body"));
                                                    }else if(whatForm=="frmProductProductions"){
                                                        resetForm($$("#"+whatForm));
                                                        $$.each(data['results'], function(key, value) {
                                                          $$("#"+whatForm+ " div.list-block div.item-content div.qty-input[data-id='"+key+"']").text(value["countproduced"]);
                                                          $$("#"+whatForm+ " div.list-block div.item-content div.qty-current[data-id='"+key+"']").text(value["count"]);
                                                        });
                                                        displayInfo(data["message"], $$("body"));
                                                    }
                                               }else{ 
                                                   if(whatForm=="checkIsUserLoggedIn"){
                                                       if(data["success"]==6){
                                                           displayAlert(data["message"], $$("body"));
                                                       }
                                                   }else{
                                                        displayAlert(data["message"], $$("body"));
                                                    }
                                               }
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
                    displayAlert(stringAlert, $$("#"+whatForm).closest("div"));
                    return false;
		}
		
    };
	
	
};

function displayAlert(a, b){
    var _fadeIn=300;
    var c="<div class='closeOverlay abs right0'><a class='relative' href=''>x</a></div><div class='errContent relative'>" + a + "</div>";
    var d=$$("<div class='errMessage1 abs' />"); 
    b.prepend(d);
    //d.html(c).fadeIn("fast", function(){$(this).delay(4000).fadeOut("fast", function(){$(this).remove();});});
    d.html(c);
    scrollToAnchor("content");
    d.fadeIn(_fadeIn, function(){
        $$("div.closeOverlay a, a", this).click(function(e){
            e.preventDefault();
            $$("div.overlayDisabler", b).remove();
            d.fadeOut(_fadeIn, function(){
                $$(this).remove();
            });
        });
    });
    
}

function displayInfo(a, b){
    var _fadeIn=300;
    var c="<div class='closeOverlay abs right0'><a class='relative' href=''>x</a></div><div class='infoContent relative'>" + a + "</div>";
    var d=$$("<div class='errMessage1 abs' />"); 
    b.prepend(d);
    //d.html(c).fadeIn("fast", function(){$(this).delay(4000).fadeOut("fast", function(){$(this).remove();});});
    d.html(c);
    d.fadeIn(_fadeIn, function(){
        $$("div.closeOverlay a, a", this).click(function(e){
            e.preventDefault();
            $$("div.overlayDisabler", b).remove();
            d.fadeOut(_fadeIn, function(){
                $$(this).remove();
            });
        });
    });
    $$(document).mouseup(function(){
        $$("div.overlayDisabler", b).remove();
            d.remove();
    });
}

function handleWithForms(form){
    var id=form.attr("id");
    form.submit(function(e){
       e.preventDefault();
       return $$(this).checkFields();
       return false;
   });
}

function checkIsUserLoggedIn(){
    window.setTimeout(function(){
        return $$("#checkIsUserLoggedIn").checkFields();
    return false;
    }, 100);
    
}

function wrapDateModal(){
    var myModal=myApp.modal({
        title: 'Izaberite datum proizvodnje',
        afterText: '<div id="calendar-inline-container"></div>'
    });
    
    var today = new Date();
    var yesterday = new Date().setDate(today.getDate() -1);
    
    var dayNames = ['Nedjelja', 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota'];
    var dayNamesShort = ['Ned', 'Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub'];
    
    var monthNames = ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Juni', 'Juli', 'Avgust' , 'Septembar' , 'Oktobar', 'Novembar', 'Decembar'];
 
var calendarInline = myApp.calendar({
    container: '#calendar-inline-container',
    value: [new Date()],
    monthNames: monthNames,
    dayNames: dayNames,
    dayNamesShort: dayNamesShort,
    closeByOutsideClick: true,
    toolbarCloseText: 'Potvrdi',
    dateFormat: 'DD, dd. MM yyyy',
    disabled: {
      to: yesterday
    },
    toolbarTemplate: 
        '<div class="toolbar calendar-custom-toolbar">' +
            '<div class="toolbar-inner">' +
                '<div class="left">' +
                    '<a href="#" class="link icon-only"><i class="icon icon-back"></i></a>' +
                '</div>' +
                '<div class="center"></div>' +
                '<div class="right">' +
                    '<a href="#" class="link icon-only"><i class="icon icon-forward"></i></a>' +
                '</div>' +
            '</div>' +
        '</div>',
    onOpen: function (p) {
        $$('.calendar-custom-toolbar .center').text(monthNames[p.currentMonth] +', ' + p.currentYear);
        $$('.calendar-custom-toolbar .left .link').on('click', function () {
            calendarInline.prevMonth();
        });
        $$('.calendar-custom-toolbar .right .link').on('click', function () {
            calendarInline.nextMonth();
        });
        
        $$('a.close-picker').on('click', function(){
            
            if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        var postData={context: "setupProductionDateFor", timestamp: p.value[0]};
        $$.ajax({
           type: "POST",
           url: pathToAjaxDispatcher,
           data: postData,
           dataType: "json",
           success: function(data){
               isAjaxLoaded=false;
                   if(data["success"]==1){
                        myApp.closeModal(myModal);
                        mainView.router.load({
                            template: Template7.templates.listProducts,
                            context: data
                        });  
                   }else{
                       
                   }
                return false;
           }, error: function(){
               isAjaxLoaded=false;
           }
        });
          
        });
    },
    onMonthYearChangeStart: function (p) {
        $$('.calendar-custom-toolbar .center').text(monthNames[p.currentMonth] +', ' + p.currentYear);
    }
}); 

    
}

function wrapInlineSubmit(a){
    var reVal=new RegExp(a.pattern);
    if(reVal.test(a.value)){
        $$(a).closest(".item-inner").find(".submit-input").removeClass("hidden");
    }else{
        $$(a).closest(".item-inner").find(".submit-input").addClass("hidden");
    }
    
}

function resetForm(form){
    form.find("input[type=text], textarea").val("");
    form.find("div.submit-input").addClass("hidden");
}

$$(document).on("submit", "form[data-action='handlewithform']", function(e){
   e.preventDefault(); 
   return $$(this).checkFields();
   return false;
});

//Check is user already loggedin
checkIsUserLoggedIn();

$$(document).on("mouseup", function(){
    $$("div.errMessage1").remove();
});

$$(document).on("click", "[name='aEEE_etype']", function(){
   if($$(this).is(":checked")){
       $$(this).closest("#frmProductOrders").find("[name='aEOE_etype']").val(1);
       $$(this).closest("#frmProductOrders").find("div.bottom-controls").find("input[type=submit]").removeClass("color-green").addClass("color-pink").val("Razduži");
       $$(this).closest("#frmProductOrders").find("div.inputs-list").find("input[type=submit]").removeClass("color-green").addClass("color-pink").val("-");
       $$(this).closest("div[data-page='products']").addClass("minus-mode");
   }else{
       $$(this).closest("#frmProductOrders").find("[name='aEOE_etype']").val(0);
       $$(this).closest("#frmProductOrders").find("div.bottom-controls").find("input[type=submit]").removeClass("color-pink").addClass("color-green").val("Zaduži");
       $$(this).closest("#frmProductOrders").find("div.inputs-list").find("input[type=submit]").removeClass("color-pink").addClass("color-green").val("+");
       $$(this).closest("div[data-page='products']").removeClass("minus-mode");
   }
});

$$(document).on("click", "[data-action='logout']", function(e){
   e.preventDefault();
   var $this=$$(this);
   if(isAjaxLoaded) return false;
        isAjaxLoaded=true;
        var postData={context: $this.attr("data-context")};
        $$.ajax({
           type: "POST",
           url: pathToAjaxDispatcher,
           data: postData,
           dataType: "json",
           success: function(data){
               isAjaxLoaded=false;
                   if(data["success"]==1){
                        mainView.router.load({
                            url: 'index.html',
                        });
                   }else{
                       
                   }
                return false;
           }, error: function(){
               isAjaxLoaded=false;
           }
        });
});