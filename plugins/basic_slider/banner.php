<script type="text/javascript" language="javascript" src="scs_includes/banner/bjqs-1.3.min.js"></script>
<style>

#banner-fade,
#banner-slide{
	margin-bottom: 60px;
}

ul.bjqs-controls.v-centered li a{
	display:block;
	padding:10px;
	background:#fff;
	color:#000;
	text-decoration: none;
}

ul.bjqs-controls.v-centered li a:hover{
	background:#000;
	color:#fff;
}

ol.bjqs-markers li a{
	padding:5px 10px;
	background:#000;
	color:#fff;
	margin:5px;
	text-decoration: none;
	
}

ol.bjqs-markers li.active-marker a,
ol.bjqs-markers li a:hover{
	background: #999;
}

p.bjqs-caption{
	background: rgba(255,255,255,0.5);
}
ul.bjqs{position:relative; list-style:none;padding:0;margin:0;overflow:hidden; display:none;}
li.bjqs-slide{position:absolute; display:none;}
ul.bjqs-controls{list-style:none;margin:0;padding:0;z-index:9999;}
ul.bjqs-controls.v-centered li a{position:absolute;}
ul.bjqs-controls.v-centered li.bjqs-next a{right:0;}
ul.bjqs-controls.v-centered li.bjqs-prev a{left:0;}
ol.bjqs-markers{list-style: none; padding: 0; margin: 0; width:100%;}
ol.bjqs-markers.h-centered{text-align: center;}
ol.bjqs-markers li{display:inline;}
ol.bjqs-markers li a{display:inline-block;}
p.bjqs-caption{display:block;width:96%;margin:0;padding:2%;position:absolute;bottom:0;}
</style>
 <!--  Outer wrapper for presentation only, this can be anything you like -->
      <div id="banner-fade">

        <!-- start Basic Jquery Slider -->
        <ul class="bjqs">
          
          <li><img src="scs_includes/banner/1.jpg" title=""></li>
          <li><img src="scs_includes/banner/2.jpg" title=""></li>
          <li><img src="scs_includes/banner/3.jpg" title=""></li>
          <li><img src="scs_includes/banner/4.jpg" title=""></li>
         <li><img src="scs_includes/banner/5.jpg" title=""></li>
        </ul>
        <!-- end Basic jQuery Slider -->

      </div>
      <!-- End outer wrapper -->

      <script class="secret-source">
        jQuery(document).ready(function($) {

          $('#banner-fade').bjqs({
		   animtype      : 'fade',
            height      : 400,
            width      : 1000,
			showcontrols    : true,
			showmarkers     : true,   
            
            responsive  : true
          });

        });
      </script>