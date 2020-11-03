
function openListing(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("yw-tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("yw-tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  var tabName = evt.currentTarget.getAttribute("tab-desc");
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

$ = jQuery;

$(document).ready(function(){
  if($.fn.fancybox) {
    $("#gallery a[rel=yw_gallery_group]").fancybox({     
        afterLoad : function(instance, current) {
          var pixelRatio = window.devicePixelRatio || 1;

          if ( pixelRatio > 1.5 ) {
            current.width  = current.width  / pixelRatio;
            current.height = current.height / pixelRatio;
          }
        },      
    });
  }

  $(".yw-contact").click(function(e){      
    e.preventDefault();
    var width = "500px";
    /*if($(window).width()< 568){
      width = "350px";
    }*/
    var subject = $(this).attr("data-subject");
    $.fancybox.open({
      src  : '/yacht-contact',
      type : 'iframe', 
      smallBtn : true,
      iframe : {
          css : {
              width : width
          }
      },
      opts : {
        afterShow : function( instance, current ) {   
        var $iframe = $('.fancybox-iframe');    
          console.log( $('input[name="your-subject"]', $iframe.contents()).val(subject) );                
        }
      }
    });
  }); 

  $(".yw-lists-content .ad-search").click(function(){
    $(".yw-lists-content .yw-search-box").fadeToggle( "slow", "linear" );
  }); 

  $(".yacht-description h3.title").click(function(){    
    if(!$(this).hasClass("opened")){      
      $(".yacht-description h3.title").removeClass("opened");
      $(".yacht-description .yw-tabcontent").removeClass('opened');
      $(this).addClass("opened");
      $(this).next().addClass("opened");
    }else{
      $(".yacht-description h3.title").removeClass("opened");
      $(".yacht-description .yw-tabcontent").removeClass('opened');
    }    
  });

  function active_sort(){
    if($(".yw-sort >a.sortActive").length>0){
      var left = $(".yw-sort >a.sortActive").position().left;

      $(".yw-sort .sort-direction").css( { "left": left+$(".yw-sort >a.sortActive").width()+12, "opacity":1} );      
    }    
  }
  
  setTimeout(function(){
    active_sort();
  }, 500);
  

  window.addEventListener("resize", function() {
    // Get screen size (inner/outerWidth, inner/outerHeight)
    active_sort();
  }, false);
 /* $(".yw-sort >a").click(function(e){
    active_sort();
  });*/

});
