<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/style.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/common.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/menu.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/skeleton.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/layout.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/font-awesome/css/font-awesome.css">
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/JQthems/blue/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/plugin/jtable/themes/lightcolor/gray/jtable.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/modal.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/dhtmlwindow.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo SITE_URL_BRANCH_ADMIN;?>/scs_css/validationEngine.jquery.css"  type="text/css" />
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-114x114.png">
	

<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/ajax.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery-1.8.24.ui.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/plugin/jtable/jquery.jtable.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery.slimscroll.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/dhtmlwindow.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/modal.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/date_time_currency_number.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery.validationEngine.js" language="javascript"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery.validationEngine-en.js" language="javascript"></script>
<script type="text/javascript" src="<?php echo SITE_URL_BRANCH_ADMIN;?>/js/jquery.validate.js" language="javascript"></script>
<script  type="text/javascript" language="javascript">
			jQuery(document).ready(function(){
				
				// hide #back-top first
				jQuery("#back-top").hide();
				
				// fade in #back-top
				jQuery(function () {
					jQuery(window).scroll(function () {
						if (jQuery(this).scrollTop() > 100) {
							jQuery('#back-top').fadeIn();
						} else {
							jQuery('#back-top').fadeOut();
						}
					});

					// scroll body to 0px on click
					jQuery('#back-top a').click(function () {
						jQuery('body,html').animate({
							scrollTop: 0
						}, 800);
						return false;
					});
				});

			});
		
		</script>
		