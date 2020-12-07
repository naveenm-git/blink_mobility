/*checkbox*/
/* $('input[type="checkbox"]').on('change', function() {
   $('input[type="checkbox"]').not(this).prop('checked', false);
}); */
/*prodcut view thumb*/
$(".mini img").click(function(){
  
  $(".maxi").attr("src",$(this).attr("src").replace("100x100","400x400"));

});
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function(a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"), c.children().first().before('<input type="button" value="-" class="minus" />'), c.children().last().after('<input type="button" value="+" class="plus" />')
    })
}
String.prototype.getDecimals || (String.prototype.getDecimals = function() {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".plus, .minus", function() {
    var a = jQuery(this).closest(".quantity").find(".qty"),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});
/*tab terms&condition*/
 // tabbed content
    // http://www.entheosweb.com/tutorials/css/tabs.asp
    $(".tab_content").hide();
    $(".tab_content:first").show();

  /* if in tab mode */
    $("ul.tabs li").click(function() {
		
      $(".tab_content").hide();
      var activeTab = $(this).attr("rel"); 
      $("#"+activeTab).fadeIn();		
		
      $("ul.tabs li").removeClass("active");
      $(this).addClass("active");

	  $(".tab_drawer_heading").removeClass("d_active");
	  $(".tab_drawer_heading[rel^='"+activeTab+"']").addClass("d_active");
	  
    /*$(".tabs").css("margin-top", function(){ 
       return ($(".tab_container").outerHeight() - $(".tabs").outerHeight() ) / 2;
    });*/
    });
    $(".tab_container").css("min-height", function(){ 
      return $(".tabs").outerHeight() + 50;
    });
	/* if in drawer mode */
	$(".tab_drawer_heading").click(function() {
      
      $(".tab_content").hide();
      var d_activeTab = $(this).attr("rel"); 
      $("#"+d_activeTab).fadeIn();
	  
	  $(".tab_drawer_heading").removeClass("d_active");
      $(this).addClass("d_active");
	  
	  $("ul.tabs li").removeClass("active");
	  $("ul.tabs li[rel^='"+d_activeTab+"']").addClass("active");
    });
	
	
	/* Extra class "tab_last" 
	   to add border to bottom side
	   of last tab 
	$('ul.tabs li').last().addClass("tab_last");*/
/*accordion*/
$('.i-accordion').on('show.bs.collapse', function(n){
  $(n.target).siblings('.panel-heading').find('.panel-title i').toggleClass('fa-chevron-down fa-chevron-up');
});
$('.i-accordion').on('hide.bs.collapse', function(n){
  $(n.target).siblings('.panel-heading').find('.panel-title i').toggleClass('fa-chevron-up fa-chevron-down');
});

/* P */
$('.accordion-2a, .accordion-2b, .accordion-3').on('show.bs.collapse', function(n){
  $(n.target).siblings('.panel-heading').find('.panel-title i').toggleClass('fa-minus fa-plus');
});
$('.accordion-2a, .accordion-2b, .accordion-3').on('hide.bs.collapse', function(n){
  $(n.target).siblings('.panel-heading').find('.panel-title i').toggleClass('fa-plus fa-minus');
});
/*tabs*/
(function($) {
  $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
    var $target = $(e.target);
    var $tabs = $target.closest('.nav-tabs-responsive');
    var $current = $target.closest('li');
    var $parent = $current.closest('li.dropdown');
		$current = $parent.length > 0 ? $parent : $current;
    var $next = $current.next();
    var $prev = $current.prev();
    var updateDropdownMenu = function($el, position){
      $el
      	.find('.dropdown-menu')
        .removeClass('pull-xs-left pull-xs-center pull-xs-right')
      	.addClass( 'pull-xs-' + position );
    };
    $tabs.find('>li').removeClass('next prev');
    $prev.addClass('prev');
    $next.addClass('next');
    
    updateDropdownMenu( $prev, 'left' );
    updateDropdownMenu( $current, 'center' );
    updateDropdownMenu( $next, 'right' );
  });
})(jQuery);

/*menu*/
(function($) {
$.fn.menumaker = function(options) {  
var cssmenu = $(this), settings = $.extend({
format: "dropdown",
sticky: false
}, options);
return this.each(function() {
$(this).find(".button").on('click', function(){
$(this).toggleClass('menu-opened');
var mainmenu = $(this).next('ul');
if (mainmenu.hasClass('open')) { 
mainmenu.slideToggle().removeClass('open');
}
else {
mainmenu.slideToggle().addClass('open');
if (settings.format === "dropdown") {
mainmenu.find('ul').show();
}
}
});
cssmenu.find('li ul').parent().addClass('has-sub');
multiTg = function() {
cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
cssmenu.find('.submenu-button').on('click', function() {
$(this).toggleClass('submenu-opened');
if ($(this).siblings('ul').hasClass('open')) {
$(this).siblings('ul').removeClass('open').slideToggle();
}
else {
$(this).siblings('ul').addClass('open').slideToggle();
}
});
};
if (settings.format === 'multitoggle') multiTg();
else cssmenu.addClass('dropdown');
if (settings.sticky === true) cssmenu.css('position', 'fixed');
resizeFix = function() {
var mediasize = 1000;
if ($( window ).width() > mediasize) {
cssmenu.find('ul').show();
}
if ($(window).width() <= mediasize) {
cssmenu.find('ul').hide().removeClass('open');
}
};
resizeFix();
return $(window).on('resize', resizeFix);
});
};
})(jQuery);
(function($){
$(document).ready(function(){
$("#cssmenu").menumaker({
format: "multitoggle"
});
});
})(jQuery);
/*slide owl*/
(function($){
$('.carousel1').owlCarousel({
items: 5, 
margin:0,
loop: true,
nav    : true,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 3
},
1170: {
items: 5,
nav:true
}
}
});  
})(jQuery);

/*slide owl*/
(function($){
$('.carousel2').owlCarousel({
items: 1, 
margin:5,
loop: true,
nav    : false,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 1
},
1170: {
items: 1,
nav:true
}
}
});  
})(jQuery);

/*slide owl*/
(function($){
$('.carousel2').owlCarousel({
items: 4, 
margin:0,
loop: true,
nav    : false,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 1
},
1170: {
items: 1,
nav:false
}
}
});  
})(jQuery);
(function($){
$('.carousel3').owlCarousel({
items: 1, 
margin:5,
loop: true,
nav    : true,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 3
},
1170: {
items: 4,
nav:true
}
}
});  
})(jQuery);
/*slide owl cart home*/
(function($){
$('.carousel4').owlCarousel({
items: 4, 
margin:10,
loop: true,
nav    : true,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 3
},
1170: {
items: 4,
nav:true
}
}
});  
})(jQuery);
(function($){
$('.carousel5').owlCarousel({
items: 4, 
margin:10,
loop: true,
nav    : true,
autoplay:true,
autoPlaySpeed: 1000,
smartSpeed :900,
navText : ["<i class='icofont-thin-left'></i>","<i class='icofont-thin-right'></i>"],
responsive: {
0: {
items: 1
},
768: {
items: 3
},
1170: {
items: 4,
nav:true
}
}
});  
})(jQuery);
/*search*/
function openSearch() {
  document.getElementById("myOverlay").style.display = "block";
}

function closeSearch() {
  document.getElementById("myOverlay").style.display = "none";
}

// Params
var sliderSelector = '.swiper-container',
options = {
init: false,
speed:2000,
slidesPerView: 3, // or 'auto'
slidesPerColumn: 2,
slidesPerGroup:1,
spaceBetween: 5,
grabCursor: true,
autoplay: {
delay:3000
},
navigation: {
nextEl: '.swiper-button-next',
prevEl: '.swiper-button-prev',
},
breakpoints: {
1023: {
  slidesPerView: 1,
  spaceBetween: 0
}
},
// Events
on: {
init: function(){
  this.autoplay.stop();
},
imagesReady: function(){
  this.autoplay.start();
  this.el.classList.remove('loading');
}
}
};
var mySwiper = new Swiper(sliderSelector, options);

// Initialize slider
mySwiper.init();

/*tabs*/
(function($) {
  $(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
    var $target = $(e.target);
    var $tabs = $target.closest('.nav-tabs-responsive');
    var $current = $target.closest('li');
    var $parent = $current.closest('li.dropdown');
		$current = $parent.length > 0 ? $parent : $current;
    var $next = $current.next();
    var $prev = $current.prev();
    var updateDropdownMenu = function($el, position){
      $el
      	.find('.dropdown-menu')
        .removeClass('pull-xs-left pull-xs-center pull-xs-right')
      	.addClass( 'pull-xs-' + position );
    };
    $tabs.find('>li').removeClass('next prev');
    $prev.addClass('prev');
    $next.addClass('next');
    
    updateDropdownMenu( $prev, 'left' );
    updateDropdownMenu( $current, 'center' );
    updateDropdownMenu( $next, 'right' );
  });
})(jQuery);


