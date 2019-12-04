$(document).ready(function(){
	function dropMenu() {
		if ($('#topnav').hasClass('responsive')) {
			$('#topnav').removeClass('responsive');
		} else {
			$('#topnav').addClass('responsive');
		}
	}
});
function getTabs() {
	$.ajax({
		url: '/assets/php/auth.php?action=isLoggedIn',
		success: function(response) {
			if (response == "yes") {
				$('#login_btn').remove();
				$('#my_account').css("display", "inline");

			} else {
				$('#message_open_menu').remove();
			}
		}
	});
}
function openMessagesFrame() {$('#message_center_main').slideDown(500);}
function closeMessagesFrame() {$('#message_center_main').slideUp(500);}

getTabs();