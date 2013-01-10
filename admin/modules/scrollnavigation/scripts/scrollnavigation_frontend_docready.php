
<script>


jQuery(document).ready(function(){
	
		$('#beadrow').css("opacity","0");
		
	
});


window.onload=function(){
	$(".carousel").jCarouselLite({
		btnNext: ".next",
		btnPrev: ".prev",
		speed: 1000,
		visible: 13,
		scroll: 5
	});
	
	$("#beadrow").animate({opacity:1.0},1000);
	//css("opacity","1");
	$('.carousel ul li').css("width","82px");
}



</script>