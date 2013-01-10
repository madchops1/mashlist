$(document).ready(function(){
	
  // MAIN NAV NICE MENU JS
  $("#block-nice_menus-3 ul li ul li, #block-nice_menus-9 ul li ul li, #block-nice_menus-10 ul li ul li").mouseover(function(){
    $('> a',$(this).parent().parent()).css('color','white') ;
    $('> a',$(this).parent().parent()).css('text-shadow','0.1em 0.1em 0.2em black') ;
  });
  $("#block-nice_menus-3 ul li ul li, #block-nice_menus-9 ul li ul li, #block-nice_menus-10 ul li ul li").mouseout(function(){
    $('> a',$(this).parent().parent()).css('color','#656666') ;
    $('> a',$(this).parent().parent()).css('text-shadow','none') ;
  });
  $("#block-nice_menus-3 ul li.menuparent, #block-nice_menus-9 ul li.menuparent, #block-nice_menus-10 ul li.menuparent").mouseover(function(){
    $('> a',$(this)).css('color','white') ;
    $('> a',$(this)).css('text-shadow','0.1em 0.1em 0.2em black') ;
  });
  $("#block-nice_menus-3 ul li.menuparent, #block-nice_menus-9 ul li.menuparent, #block-nice_menus-10 ul li.menuparent").mouseout(function(){
    $('> a',$(this)).css('color','#656666') ;
    $('> a',$(this)).css('text-shadow','none') ;
  });
  
  /*
   * FORCING HEIGHTS
   * USES MIN HEIGHT IS IMPORTANT
   * USING HEIGHT ITSELF CAUSES ISSUES ON PAGES WITH VERTICALLY EXPANDING ELEMENTS
   * 
   */
  
  // FORCES SIDEBAR HEIGHT TO MAX
  $('#sidebar-first').css('min-height',$('#sidebar-first').parent('#main-inner').height());
  
  // FORCES CONTENT TABS TO MAX ON PLAYLIST PAGE
  //$('body.node-type-playlist #content-tabs').height($('body.node-type-playlist #content-tabs').parent('#content-region-inner').height());
  $('body.node-type-playlist #playlist-wrapper').css('min-height',$('body.node-type-playlist #content-content').parent('#content-inner-inner').height());
  
  // FORCES AJAXPAGE > RIGHTCOLUMN TO MAX 
  $('#ajaxpage #right_column').css('min-height',$('#ajaxpage #right_column').parent('#ajaxpage').height());
  
  // FORCES SECOND PANEL TO MAX WHEN PANELS ARE USED
  $('.panel-panel.panel-col-last').css('min-height',$('.panel-panel.panel-col-last').parent('.panel-display').height());
  
  // FORCES MENU TO MAX ON NODE EDIT PAGES
  $('body.page-node.node-type-song form div.item-list').css('min-height',$('body.page-node form div.item-list').parent('#content-content').height());
  
  // FORCES CONTENT INNER TO MAX
  $('div#content-inner').css('min-height',$('#sidebar-first').parent('#main-inner').height());
  
  /*
   * FADING OUT MESSAGE MODALS
   * 
   */
  
  if($('div.content-messages-inner div.messages').length){
    $('div.content-messages-inner div.messages').click(function(){
	  $(this).fadeOut();
    });
  }
  
  if($('div.content-messages-admin-inner div.messages').length){
    $('div.content-messages-admin-inner div.messages').click(function(){
	  $(this).fadeOut();
    });
  }
  
  if($('div.messages').length){
    $('div.messages').click(function(){
	  $(this).fadeOut();
    });
  }
  
  /*
   * SHRINKING H1 TITLE TEXT IF TO LONG
   */
  if($('#preface-bottom-inner h1.title').length){
	  var titleText = $('#preface-bottom-inner h1.title').html();
	  var titleTextLength = 0;
	  //if(titleText.length != 0){
	    
		  titleTextLength = titleText.length;
		  //alert("LENGTH: " + titleTextLength);
		  
		  if(titleTextLength > 24){
			  $('#preface-bottom-inner h1.title').css('font-size','40px');
		  }else if(titleTextLength > 54){
			  $('#preface-bottom-inner h1.title').css('font-size','27px');
		  }
	  //}
  }
  
  /*
   * SETUP MEET THE TEAM SCROLLBARS
   */
  $('div.field-field-team-bio').jScrollPane({
    verticalDragMinHeight: 26,
    verticalDragMaxHeight: 26
  });
  
  /*
   * ADD <br clear='all' /> before forms
   */
  $('#node-form div:first').append('<br clear="all" />');
  
  if($("form#user-register").length){
	  $("#got-music-form-title").remove();
	  $("form#user-register div:first").prepend("<h1 id='got-music-form-title'>Sign Up If You've Got Music</h1>");
	  $("form#user-register br:last").remove();
	  $("form#user-register").append("<br clear='all'/>");
  }
  
  /*if($())*/
  
});