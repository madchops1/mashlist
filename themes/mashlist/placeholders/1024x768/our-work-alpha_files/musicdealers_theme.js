$(document).ready(function() {

	$('.login_form #mb-login-help-form input[name=help]').change(function() { 
		if ($(this).attr('id') === 'edit-help-0') {
			$('.login_form .forget_login').slideDown();
			$('.login_form .login_help').slideUp();
		} else {
			$('.login_form .forget_login').slideUp();
			$('.login_form .login_help').slideDown();
		}
	
	});
	
	function moveFilters() {
    var class1 = '';
    var classes_parent = '';
    var parent_class = '';
    var second_column = $('#second-column-content > .views-widget-filters, #second-column-content > .views-operator-filters');
    
    if(second_column.length > 0) {
      classes_parent = second_column.attr('id');
      
      $('#holder').append(second_column).hide();
    } else {
      $('#second-column-content > .dtool-main-filter-secondary').each(function(i) {
        $('#holder').append($(this)).hide();
      });
      $('#second-column-top, #second-column-bottom').hide();
      
    }
    $('#third-column-content > .dtool-main-filter-secondary').each(function(i) {
      $('#holder').append($(this)).hide();
    });
    $('#third-column-top, #third-column-bottom').hide();
    $('#dtool-advanced-filters > .dtool-advanced-main-filter').removeClass('sub-filter-on');
  }
  
  function adjustHeight(the_base_id, is_main) {
    var height = $('#second-wide').height() -1;
	  var widget_height = $('#' + the_base_id).height();
	  
	  var is_second_column = $('#second-column-content > .views-widget-filters').length;
	  if(is_second_column > 0) {
	   widget_height = $('#dtool-advanced-filters').height();
	   
	   var widget_height_third = $('#third-column').height();
	   if(widget_height_third > widget_height) {
	     widget_height = widget_height_third;
	   }
	  }
	  
	  var second_column_height = $('#second-column').height();
	  if(second_column_height > widget_height && is_main == 0) {
      widget_height = second_column_height;
    }

    if(widget_height != height && widget_height >= 250) {
      $('#second-wide, #row4 > .first').height(widget_height);

      $('#second-column-content').height(widget_height-20);
      if(is_second_column > 0) {
        $('#third-column-content').height(widget_height-22);
        $('#dtool-advanced-filters').height(widget_height);
      }
      if(is_main == 1) {
        $('#' + the_base_id).height(widget_height);

      }
    } else {
      if(widget_height < 250) {
        height = 250;
      }

      $('#' + the_base_id + '-secondary').height(height-20);
      $('#second-wide, #row4 > .first').height(height);
      $('#second-column-content').height(height-20);
      if(is_second_column > 0) {
        $('#third-column-content').height(height-22);
        $('#dtool-advanced-filters').height(height);
      }
      if(is_main == 1) {
        $('#' + the_base_id).height(height);

      }
    }
  }
  
  function makeFilterButton(the_base_id, button_text, is_main_button) {
    var $this = $(the_base_id);
    $('#' + the_base_id + '-filter-button').remove();

    if(is_main_button) {
      $('#dtool-filters-selected').append('<div id="' + the_base_id + '-filter-button" class="filter-button main-button"><div class="filter-button-left"></div><div class="filter-button-x"></div><div class="filter-button-text">' + button_text + '</div><div class="filter-button-right"></div></div>');
    } else {
      $('#dtool-filters-selected').append('<div class="filter-button sub-button" id="' + the_base_id + '-filter-button"><div class="sub-button-left"></div><div class="sub-button-x"></div><div class="sub-button-text">' + button_text + '</div><div class="sub-button-right"></div></div>');
    }
    
    get_results_number();
    filter_button_bind();     
  }
	
	if($('#dtool-filters-selected').length > 0) {
    if($('#dtool-filters-selected').html() == '') {
      $('#second-wide * input:text').val('');
      $('#second-wide * select').not('#edit-items-per-page').attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
      $('#second-wide * :checked').attr('checked', false).removeAttr('checked');
    } else {
      $('#second-wide * :checked').not('#edit-items-per-page').each(function() {
        var name = $(this).attr('id');
        var attr_value = $(this).attr('value');
        var length = name.length;
        var attr_length = attr_value.length + 1;
  
        if($('#dtool-filters-selected .' + name).length == 0 && ($('#dtool-filters-selected .' + name.substring(0, length - attr_length)).length == 0)) {
          $(this).attr('checked', false).removeAttr('checked');
        }
      });
      
      $(':text').each(function() {
        if($('#dtool-filters-selected .' + $(this).attr('id')).length == 0) {
        $(this).val('');
        }
      });
      
      filter_button_bind();
    }
    
    /*Apply filter-children to the right divs*/
    $('.dtool-main-filter-secondary').each(function() {
      var $this = $(this);
      var the_id = $this.attr('id');
      var the_base_id = the_id.substring(0, the_id.length - 10);
      
      $('#' + the_base_id + '-wrapper').addClass('filter-children');
      $('#' + the_base_id + '-label').addClass('filter-children-option');
      
      $('.dtool-main-filter-tertiary', $this).each(function() {
        var $this_tertiary = $(this);
        var id = $this_tertiary.attr('id');
        var tertiary_base_id = id.substring(0, id.length - 9);
        $('#' + tertiary_base_id + '-wrapper').addClass('tertiary-down');
        $('#' + tertiary_base_id + '-label').addClass('filter-children-tertiary');
      });
    });
	}	
 
 /*Get new results number*/
  function get_results_number() {
    $('#number').html('-');
    
    $.get(Drupal.settings.basePath + 'get-results-number', $('form').serialize(), function(data) {
        if(data == 0) {
          $('#number').html('0');
        } else {
          $('#number').html(data);
        }
      }, 'json');
  }
	
	/*Remember on refresh or submit---*/
	function filter_button_bind() {
	 $('#dtool-filters-selected > .filter-button').bind('click', function() {
	   var $this = $(this);
	   var id = $this.attr('id');
	   var base_id = id.substring(0, id.length - 14);
	   var widget = $('#' + base_id);
	   
	   if(widget.length > 0) {
	     var type = widget.attr('type');
	     
	     if(type === 'checkbox' || type === 'radio') {
	       widget.attr('checked', false).removeAttr('checked');
	       $('#' + base_id + '-wrapper').removeClass('sub-filter-selected');
	       
	       $('#' + base_id + '-secondary .sub-filter-selected input:checkbox').removeAttr('checked').change()
	     }

	     if(type.indexOf('select') != -1) {
         $('#' + base_id).attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
       }
      
       if(type == 'text') {
         $('#' + base_id).val('');
       }
	   } else {
	     $('#' + base_id).attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
	     $('#' + base_id + '-op').attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
	     $('#' + base_id + '-min').val('');
       $('#' + base_id + '-max').val('');
	     $('#' + base_id + '-value').val('');
	     
	   }
	   $this.remove();
	   get_results_number();
	 });
	}
  
  
  $('#dtool-filters-selected > .filter-button').each(function() {
    var $this = $(this);
    var id = $this.attr('id');
    var base_id = id.substring(0, id.length - 14);
    
    $('#' + base_id + '-wrapper').addClass('sub-filter-selected');

  });
	
//---------
	
	/*Clear All*/
	$('#clear-all-button').click(function() {
    $('#dtool-filters-selected').html('');
    $('#second-wide input:text').val('');
    $('#second-wide select').not('#edit-items-per-page').attr('selectedIndex', '0').children('option:selected').removeAttr('selected');
    $('#second-wide input:checkbox, #second-wide input:radio').attr('checked', false).removeAttr('checked');
    $('#second-wide .sub-filter-selected, #second-wide .tertiary-filter-on').removeClass('sub-filter-selected').removeClass('tertiary-filter-on');
    
    get_results_number();
	});
	
	/*Show advanced filters after clicking on the Advanced label*/
	$('#dtool-advanced-label').click(function() {
    moveFilters();
	  $('.views-operator-filters, .views-widget-filters').hide();
	  $('.views-widget-filters div').removeClass('sub-filter-on').removeClass('tertiary-filter-on');
	  $('.dtool-main-filter').removeClass('main-filter-on');
	  $(this).addClass('main-filter-on');
	  
	  $('#dtool-advanced-filters').show();
	  var height = $("#dtool-advanced-filters").height();
	  $('#second-wide, #row4 > .first').height(height);
	  $('#second-column-content').height(height-20);
	  $('#second-column-content, #second-column-top, #second-column-bottom').show();
	});
	
	/*Show filter options after clicking on a filter*/
	$('.dtool-main-filter').click(function() {
	  moveFilters();
	  $('#third-column, #second-wide > .views-operator-filters, #second-wide > .views-widget-filters').hide();
	  
	  var the_id = $(this).attr('id');
	  var filter_id = the_id.substring(0, the_id.length - 6);
	  
	  if(!$('#' + the_id).hasClass('dtool-advanced-main-filter')) {
	   $('#dtool-advanced-filters').hide();
	   $('#dtool-advanced-label').removeClass('main-filter-on');
	   $('.main-filter-on').removeClass('main-filter-on');
	   $(this).addClass('main-filter-on');
	   $('#' + filter_id).show();
	   adjustHeight(filter_id, 1);
	  } else {
	   $(this).addClass('sub-filter-on');
	   $('#second-column-content').append($('#' + filter_id + '-operators').show());
	   $('#second-column-content').append($('#' + filter_id).show()).show();
	   
	   adjustHeight(filter_id, 0);
	  }
 
    $('#second-column-top, #second-column-content, #second-column-bottom').show();
	});
	
	/*Get new results number*/
	$('#second-wide * input:text').blur(function() {
    var $this = $(this);
    var id = $this.attr('id');
    var base_id = id;
    var button_text = '';
    
    if($this.val !== '') {
      if(id.indexOf('-value-value') !== -1) {
        base_id = base_id.substring(0, base_id.length - 6);
      } else {
        if(id.indexOf('-min') !== -1 || id.indexOf('-max') !== -1) {
          base_id = base_id.substring(0, base_id.length - 4);
        }
      }
      
      if($('#dtool-advanced-label').hasClass('main-filter-on')) {
        button_text += $('#dtool-advanced-filters > .sub-filter-on:visible').text() + ': ';
        if($('#' + base_id + '-min-wrapper').is(':visible')) {
          button_text += $('#' + base_id + '-op > option:selected').text() + ' ' + $('#' + base_id + '-min').val() + ' and ' + $('#' + base_id + '-max').val();
          $('#' + base_id + '-value').val('');
        } else {
          button_text += $('#' + base_id + '-op > option:selected').text() + ' ' + $this.val();
          $('#' + base_id + '-min').val('');
          $('#' + base_id + '-max').val('');
        }
        
      }
      makeFilterButton(base_id, button_text);
    }
	});
	
	/*Get new results number */
	$('#second-wide * select').change(function() {
    var $this = $(this);
    var id = $this.attr('id');
    
    if(id.indexOf('-op') === -1) {
      var button_text = '';
      var is_main = 1;
      var operator = $('#' + id + '-op');
      
      if($('#dtool-advanced-label').hasClass('main-filter-on')) {
        is_main = 1;
        button_text += $('#dtool-advanced-filters > .sub-filter-on:visible').text() + ': ';
        if(operator.length > 0) {
          button_text += $('#' + id + '-op > option:selected').text() + ' ';
        }
      } else {
        button_text += $('.main-filter-on').text() + ': ';
        if(operator.length > 0) {
          button_text += $('#' + id + '-op > option:selected').text() + ' ';
        }
      }
      button_text += $('#' + id + ' > option:selected').text();
      makeFilterButton(id, button_text, is_main);
    } else {
      var base_id = id.substring(0, id.length - 3);
      var button_text = '';
      
      if($('#' + base_id + '-min-wrapper').is(':visible')) {
        $('#' + base_id + '-min').blur();
      } else {
        $('#' + base_id + '-value').blur();
      }
      
      if($('#dtool-advanced-label').hasClass('main-filter-on')) {
        button_text += $('#dtool-advanced-filters > .sub-filter-on:visible').text() + ': ';
        if($('#' + id).length > 0) {
          var operator_text = $('#' + id + ' > option:selected').text();
          button_text += operator_text + ' ';
          
          if(operator_text == 'Is empty (NULL)' || operator_text == 'Is not empty (NOT NULL)') {
            $('#' + base_id).attr('selectedIndex', '0').children('option:selected').removeAttr('selected');
          }
        }
      } else {
        button_text += $('.main-filter-on').text() + ': ';
      }
      
      button_text += $('#' + base_id + ' > option:selected').text();
      
      makeFilterButton(base_id, button_text, 1);
    }
	});
	
	/*Get new results number */
	$('#second-wide *:radio').change(function() {
    var $this = $(this);
    var id = $this.attr('id');
    var the_class = id.substring(0, id.length - 3);
    $('#dtool-filters-selected > div[id*="' + the_class + '"]').click();

    if(id.substring(id.length - 3) != 'All') {
      var button_text = '';
      if($('#dtool-advanced-label').hasClass('main-filter-on')) {
        button_text += $('#dtool-advanced-filters > .sub-filter-on:visible').text() + ': ';
        button_text += $this.text(); 
      } else {
        button_text += $('.main-filter-on').text() + ': ' + $this.text();
      }
      
      if($('#' + id + '-filter-button').length == 0) {
        button_text +=  $('#' + id + '-wrapper .option').text();
        if($this.parents('.dtool-main-filter-secondary').length == 0) {
          makeFilterButton(id, button_text, 1);
        } else {
          makeFilterButton(id, button_text, 0);
        }
      }
    }
	});
	
	/*Get new results number */
  $('#second-wide *:checkbox').change(function() {
    var $this = $(this);
    var id = $this.attr('id');
    var type = $this.attr('type');
    var secondary_filter = $this.parents('.dtool-main-filter-secondary');
    
    if(secondary_filter.length == 0) {
      $('.sub-filter-on, .tertiary-filter-on').not('#' + id + '-wrapper, .dtool-advanced-main-filter').removeClass('sub-filter-on').removeClass('tertiary-filter-on');
    } else {
      var secondary_filter_id = secondary_filter.attr('id');
      var length2 = secondary_filter_id.length;
      if(secondary_filter_id.charAt(length2 - 10) == 's') {
        length2 -= 1;
      }
      
      $('.sub-filter-on, .tertiary-filter-on').not('#' + secondary_filter_id.substring(0, length2 - 10) + '-wrapper, .dtool-advanced-main-filter').removeClass('sub-filter-on').removeClass('tertiary-filter-on');
    }
    
    if(id.substring(id.length - 3) == 'All') {
      return false;
    }
    
    if(!$('#' + id + '-wrapper').hasClass('sub-filter-selected')) {
      
      $this.attr("checked", "checked");
      
      $('#' + id + '-wrapper').addClass('sub-filter-selected');
      
      var button_text = '';
      if($('#dtool-advanced-label').hasClass('main-filter-on')) {
        button_text += $('#dtool-advanced-filters > .sub-filter-on:visible').text() + ': ';
        
      } else {
        button_text += $('.main-filter-on').text() + ': ' + $this.text();
      }
      
      if($('#' + id + '-filter-button').length == 0) {
        button_text +=  $('#' + id + '-wrapper .option').text();
        if($this.parents('.dtool-main-filter-secondary').length == 0) {
          makeFilterButton(id, button_text, 1);
        } else {
          makeFilterButton(id, button_text, 0);
        }
      }
      
      if(secondary_filter.length > 0) {
        var secondary_id = secondary_filter.attr('id');
        var secondary_base_id = secondary_id.substring(0, secondary_id.length - 10);
        if(!$('#' + secondary_base_id + '-wrapper').hasClass('sub-filter-selected')) {
          $('#' + secondary_base_id).attr('checked', true).change();
        }
        var tertiary_filter = $this.parents('.dtool-main-filter-tertiary');
        if(tertiary_filter.length > 0) {
          var tertiary_id = tertiary_filter.attr('id');
          var tertiary_base_id = tertiary_id.substring(0, tertiary_id.length - 9);
          
          if(!$('#' + tertiary_base_id + '-wrapper').hasClass('tertiary-filter-on')) {
            $('#' + tertiary_base_id).attr('checked', true).change();
          }
        }
      }
      
    } else {
      $('#' + id + '-wrapper').removeClass('sub-filter-selected');
      
      var main = $('.main-filter-on').text();
      
      $('#' + id + '-filter-button').remove();
      
      if($('#' + id + '-secondary').is(':visible')) {
        $('#' + id + '-secondary .sub-filter-selected input:checkbox').removeAttr('checked').change();
      }
    }
    
    filter_button_bind();
	});
	
	/*Show suboptions after clicking on one of a filter's main options*/
	$('.filter-children-option').click(function(event) {
    var $this = $(this);
    var the_id = $this.attr('id');
    var the_base_id = the_id.substring(0, the_id.length - 6);
    var is_second_column = $('#second-column-content > .views-widget-filters').length;
    
    if(is_second_column) {
      $('.tertiary-filter-on').removeClass('tertiary-filter-on');
      $('#' + the_base_id + '-wrapper').addClass('tertiary-filter-on');
      $('#holder').append($('#third-column-content > .dtool-main-filter-secondary')).hide();
      $('#third-column-content').html('').append($('#' + the_base_id + '-secondary').show().removeClass('sub-filter-selected')).show();
      $('#second-column, #third-column-top, #third-column-bottom, #third-column').show();
    } else {
      moveFilters();
      $('.sub-filter-on').removeClass('sub-filter-on');
      //$('.sub-filter-selected').removeClass('sub-filter-selected');
      $('#' + the_base_id + '-wrapper').addClass('sub-filter-on');
      $('#second-column-content').html('').append($('#' + the_base_id + '-secondary').show().removeClass('sub-filter-on')).show();
      $('#second-column-top, #second-column-bottom').show();
    }
    var is_main = 0;
    adjustHeight(the_base_id, is_main);
	});
  
  /*Show the subsuboptions if possible after clicking on a suboption*/
  $('.filter-children-tertiary').click(function() {
    var $this = $(this);
    var id = $this.attr('id');
    var base_id = id.substring(0, id.length - 6);
    
    $('#second-wide * .dtool-main-filter-tertiary:visible').hide();
    $('#second-wide * .tertiary-down-expand').removeClass('tertiary-down-expand');
    $('#' + base_id + '-wrapper').addClass('tertiary-down-expand');
    $('#' + base_id + '-tertiary').show();
    
    adjustHeight('third-column-content', 0);
  });
});
