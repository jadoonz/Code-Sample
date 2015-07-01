<?php get_header(); ?>
<?php
$bg = array('Banner-1.jpg', 'Banner-2.jpg', 'Banner-3.jpg', 'Banner-4.jpg', 'Banner-5.jpg', 'Banner-6.jpg', 'Banner-7.jpg', 'Banner-8.jpg', 'Banner-9.jpg', 'Banner-10.jpg', 'Banner-11.jpg', 'Banner-12.jpg', 'Banner-13.jpg', 'Banner-14.jpg', 'Banner-15.jpg', 'Banner-16.jpg', 'Banner-17.jpg', 'Banner-18.jpg', 'Banner-19.jpg', 'Banner-20.jpg', 'Banner-21.jpg', 'Banner-22.jpg', 'Banner-23.jpg', 'Banner-24.jpg', 'Banner-25.jpg', 'Banner-26.jpg', 'Banner-27.jpg'); // array of filenames
$i = rand(0, count($bg) - 1); // generate random number size of the array
$selectedBg = "$bg[$i]"; // set variable equal to which random filename was chosen

$sub_domain_url = get_sub_domain_url();
$prefix = 'eto_';
?>
<!--     **************************Section Banner Start********************** -->
<div class="banner text-center" style="background: url(<?php echo get_template_directory_uri(); ?>/img/bg_array/<?php echo $selectedBg; ?>);">
    <div class="container">
        <div class="empty_box"></div>
        <h1><?php echo eto_get_option($prefix . 'banner_1'); ?></h1>
        <?php echo eto_get_option($prefix . 'banner_2'); ?><br/>
        <!--<br/><br/><br/>-->
        <!--<strong><?php //echo eto_get_option($prefix . 'banner_3');   ?></strong><br/>-->

        <div class="banner_ions col-sm-8 col-sm-offset-2" style="margin-bottom:25px !important;">
            <a class="btn btn-social btn-lg btn-linkedin" href="https://login.abc.com/signup/linkedin">
                <i class="fa fa-linkedin"></i> Sign in with LinkedIn
            </a>                
        </div>

        <div style="margin-top:25px !important; margin-bottom:100px;">
            <iframe width="875" height="465" src="https://www.youtube.com/embed/xyb" frameborder="0" allowfullscreen></iframe>
        </div>

    </div>
    <a href="<?php echo eto_get_option($prefix . 'banner_8_link'); ?>" target="_blank"
       class="chorome_ico">
        <div class="chorom text-center">
            <img src="<?php echo eto_get_image($prefix . 'banner_8_icon') ?>" class="img-responsive">
            <?php echo eto_get_option($prefix . 'banner_8'); ?>
        </div>
    </a>

</div>
<!--     **************************Section Banner End********************** -->
<!--     **************************Section about Start********************** -->
<section id="about" class="text-center">
    <div class="container">
        <h2><?php echo eto_get_option($prefix . 'about_1'); ?></h2><br>
        <?php echo eto_get_option($prefix . 'about_2'); ?><br><br>

        <?php echo eto_get_option($prefix . 'about_3'); ?>
    </div>
</section><!-- end of about -->
<!--     **************************Section about End********************** -->
<!--     **************************Section partners_logos Start********************** -->
<section id="partners_logos">
    <div class="container">
        <div id="fade" style="float-left;">

            <?php
            $args = array(
                'post_type' => 'brand-logo',
                'order' => 'ASC',
                'orderby' => 'ID',
                'posts_per_page' => 100
            );
            $loop = new WP_Query($args);
            while ($loop->have_posts()) : $loop->the_post();
                $image1_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                ?>
                <img src="<?php echo $image1_url; ?>" class="img-responsive">
            <?php endwhile; ?>
        </div>
    </div>
    <!-- end of container -->
</section><!-- end of partners_logos -->
<!--     **************************Section partners_logos End********************** -->
<!--     **************************Section prospecting Start********************** -->
<section id="prospecting" class="text-center">
    <div class="container">
        <h2 class=""><?php echo eto_get_option($prefix . 'prospecting_1'); ?></h2>

        <div class="row">
            <?php
            $args = array(
                'post_type' => 'prospecting',
                'posts_per_page' => 8,
                'order' => 'ASC',
                'orderby' => 'ID'
            );
            $loop = new WP_Query($args);
            while ($loop->have_posts()) : $loop->the_post();
                $image1_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                $meta_pros = get_post_meta(get_the_ID()); //echo "<pre>"; print_r($meta);
                ?>
                <div class="col-md-3 col-sm-6 item">
                    <?php echo empty($meta_pros['url'][0]) ? '<a>' : '<a href="' . $meta_pros['url'][0] . '"
                           data-toggle="modal" onclick="jQuery(\'#planId\').val(\'\');">'; ?><img
                        src="<?php echo $image1_url ?>"><?php echo empty($meta_pros['url'][0]) ? '</a>' : '</a>'; ?>
                        <?php the_title(); ?>
                </div>
            <?php endwhile; ?>
        </div>            
    </div>
</section><!-- end of prospecting  -->
<!--     **************************Section prospecting End********************** -->

<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">

    <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
        <li data-target="#carousel-example-generic" data-slide-to="3"></li>
        <li data-target="#carousel-example-generic" data-slide-to="4"></li>
    </ol>


    <div class="carousel-inner">
        <div class="item active">
            <section id="how_it_works_00">
                <div class="container">            
                    <div class="col-md-5 text-center"><div style="vertical-align:middle; width:400px; height:360px; display:table-cell; color:#000;">Watch the explainer video demo</div></div>
                    <?php /* <div class="col-md-7" style="text-align:right;"><iframe width="642" height="362" src="https://www.youtube.com/embed/QJ_PdSYAeP8" frameborder="0" allowfullscreen></iframe></div>            */ ?>
                    <div class="col-md-7" style="text-align:right;"><iframe width="642" height="362" src="https://www.youtube.com/embed/nexSFAYJupM" frameborder="0" allowfullscreen></iframe></div>            
                </div>
            </section>
        </div>

        <div class="item ">
            <section id="how_it_works_01">
                <div class="container">            
                    <div class="col-md-5 text-center"><div style="vertical-align:middle; width:400px; height:360px; display:table-cell; color:#000;">Sign Up Now, Free</div></div>
                    <div class="col-md-7" style="text-align:right;"><img style="width:auto; height:auto;" src="<?php echo get_template_directory_uri(); ?>/img/01_signup.gif"  /></div>            
                </div>
            </section>
        </div>

        <div class="item ">
            <section id="how_it_works_02">
                <div class="container">            
                    <div class="col-md-5 text-center"><div style="vertical-align:middle; width:400px; height:360px; display:table-cell; color:#000;">Install Chrome Extension</div></div>
                    <div class="col-md-7" style="text-align:right;"><img style="width:auto; height:auto;" src="<?php echo get_template_directory_uri(); ?>/img/02_chrome_ext.gif"  /></div>            
                </div>
            </section>
        </div>

        <div class="item ">
            <section id="how_it_works_03">
                <div class="container">            
                    <div class="col-md-5 text-center"><div style="vertical-align:middle; width:400px; height:360px; display:table-cell; color:#000;">Add Sales Leads on LinkedIn</div></div>
                    <div class="col-md-7" style="text-align:right;"><img style="width:auto; height:auto;" src="<?php echo get_template_directory_uri(); ?>/img/03_add_sales_lead_li.gif"  /></div>            
                </div>
            </section>
        </div>

        <div class="item ">
            <section id="how_it_works_04">
                <div class="container">            
                    <div class="col-md-5 text-center"><div style="vertical-align:middle; width:400px; height:360px; display:table-cell; color:#000;">Add Sales Leads with the abc Search Engine</div></div>
                    <div class="col-md-7" style="text-align:right;"><img style="width:auto; height:auto;" src="<?php echo get_template_directory_uri(); ?>/img/04_add_sales_lead_se.gif"  /></div>            
                </div>
            </section>
        </div>

    </div>


    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        <div> <span class="glyphicon glyphicon-chevron-left"></span> </div>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        <div> <span class="glyphicon glyphicon-chevron-right"></span> </div>
    </a>
</div>  


<!--     **************************Section how it works Start******************** -->
<section id="how_it_works_05">
    <div>
        <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/how-it-works-flow.jpg"  />               
    </div>
</section>
<!--     **************************Section how it works End*********************** -->

<!--     **************************Section email and phone validation Start******************** -->


<div style="background: url(<?php echo get_template_directory_uri(); ?>/img/Manuel-Phone-&-Email-Validation.jpg) no-repeat scroll center center rgba(0, 0, 0, 0);">
    <section id="validation" class="text-center">
        <div class="container custom-boxes">
            <div  style="background: url(<?php echo get_template_directory_uri(); ?>/img/map.png)  no-repeat scroll 0% 0% / contain  transparent; position: absolute; height: 100%; top: 63px; width: 95%" ></div>
            <h2 style="color: rgb(255, 255, 255); margin-bottom: 45px;">Email & Phone Validation</h2>
            <div class="row">
                <div class="col-md-6 col-sm-6 padright5">
                    <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/Email-Validation.png">
                </div>
                <div class="col-md-6 col-sm-6 padleft5">
                    <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/Manuel-Phone.png">
                </div>
            </div>            
        </div>
    </section>
</div>


<!--     **************************Section email and phone validation end******************** -->

<!--     **************************Section benefits and value Start******************** -->
<section id="benefits_and_value">       	
    <div>
        <img style="width:100%" class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/benefits-and-value.jpg"  />
    </div>
</section>
<!--     **************************Section benefits and value End*********************** -->

<!--     **************************Section sign_up Start******************** -->
<section id="sign_up">
    <div class="container text-center">
        <?php echo eto_get_option($prefix . 'signup_1'); ?><br>
        <button class="btn btn-default" href="#signInToday" data-toggle="modal"><?php echo eto_get_option($prefix . 'signup_2'); ?></button>
    </div>
</section>
<!--     **************************Section sign_up End*********************** -->
<!--     **************************Section customer Start******************** -->
<section id="customer">
    <h2 class="text-center"><?php echo eto_get_option($prefix . 'customer_success_1'); ?></h2>

    <div class="container">
        <div class="row">
            <?php
            $args = array(
                'post_type' => 'success-story',
                'order' => 'ASC',
                'orderby' => 'date',
                'posts_per_page' => 12
            );
            $loop = new WP_Query($args);
            while ($loop->have_posts()) : $loop->the_post();
                $image1_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                if (class_exists('Dynamic_Featured_Image')) {
                    global $dynamic_featured_image;
                    $featured_images = $dynamic_featured_image->get_featured_images();
                }
                ?>
                <div class="col-md-3 col-sm-4 col-xs-12 clients">
                    <div class="overlap"><img
                            src="<?php echo isset($featured_images[0]['full']) ? $featured_images[0]['full'] : '' ?>"
                            alt="logo1"></div>

                    <blockquote class="text-center">
                        <?php echo get_the_content(); ?>
                    </blockquote>

                    <img src="<?php echo $image1_url; ?>" class="img-responsive" alt="logo12">
                </div>
            <?php endwhile; ?>

        </div>
    </div>
</section><!-- end of customer -->
<!--     **************************Section customer End*********************** -->
<!--     **************************Section insight Start********************** -->
<section id="insight" class="text-center">
    <div class="container">
        <img src="<?php echo eto_get_image($prefix . 'insight_section_3') ?>">
        <strong><?php echo eto_get_option($prefix . 'insight_section_1'); ?> <span><?php echo eto_get_option($prefix . 'insight_section_2'); ?></span></strong>
        <?php echo do_shortcode('[contact-form-7 id="136" title="Subscription Form"]') ?>
    </div>
</section>
<!--     **************************Section insight End*********************** -->
<!--     **************************Section pricing Start*************************** -->

<section  id="pricing"> 

    	<div style="width:90%; margin:0 auto;">
        <div class="title">
            <?php echo eto_get_option($prefix . 'pricing_1'); ?>

            <div class="border-bottom-title"></div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <?php
                $args = array(
                    'post_type' => 'packages',
                    'posts_per_page' => 5,
                    'order' => 'ASC',
                    'orderby' => 'ID'
                );
                $loop = new WP_Query($args);
                $i = 1;
                $tr_packages = '';
                $tr_investment = '';
                $tr_monthly_leads = '';
                $tr_additional_leads = '';
                $tr_call_dedicated = '';
                $tr_buy_now = '';
                while ($loop->have_posts()) : $loop->the_post();
                    $image1_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                    $meta = get_post_meta(get_the_ID()); 
                    $on_call_deduction = 'fa-close';
                    $plan_id = str_replace(' ', '_', strtolower(the_title('','',false)));
					$title = the_title('','',false);
                   
                    if ($title == 'Rainmaker') {
                        $on_call_deduction = 'fa-check';
                    } else if ($title == 'Wolf of Wall Street') {                        
                        $on_call_deduction = 'fa-check';
                    }
                    if ($i === 1) {
                        $tr_packages = '<td class="blue-bg">Packages</td>';
                        $tr_investment = '<td class="blue-bg">Monthly Investment</td>';
                        $tr_monthly_leads = '<td class="blue-bg">Monthly Leads</td>';
                        $tr_additional_leads = '<td class="blue-bg">Price/Additional Lead</td>';
                        $tr_call_dedicated = '<td class="blue-bg">On Call Dedicated Sales Research Team</td>';
                        $tr_buy_now  = '<td class="blue-bg">Buy Now</td>';
                    }
                    $tr_packages .='<td class="pack-title">' . $title . '</td>';
                    $tr_investment .='<td>$'.$meta['monthly_investment'][0].'</td>';
                    $tr_monthly_leads .='<td>'.$meta['contacts_per_month'][0].'</td>';
                    $tr_additional_leads .='<td><span class="disabled-text">$'.$meta['price_per_contact'][0].'</span></td>';
                    $tr_call_dedicated .='<td><i class="fa ' . $on_call_deduction . '"></td>';
                        $tr_buy_now .='<td><button href="#signInToday" data-toggle="modal" data-id="'.$plan_id.'" class="btn choose-plan-btn holder btn-small btn-buynow">Buy Now</button></td>';
                    $i ++;
                endwhile;
                ?>
                <tr><?php echo $tr_packages; ?></tr>
                <tr><?php echo $tr_investment; ?></tr>
                <tr><?php echo $tr_monthly_leads; ?></tr>
                <tr><?php echo $tr_additional_leads; ?></tr>
                <tr><?php echo $tr_call_dedicated; ?></tr>
                <tr><?php echo $tr_buy_now; ?></tr>
            </table>
        </div>
        </div>
 
    <p class="text-center">Not Ready To Choose A Plan?<br>
        No Worries, Use our pay-per-lead model and pay as you go.<br>
        <button href="#signInToday" data-toggle="modal" data-id="buyonecredit" class="btn btn-small btn-buynow">$1.50 / Lead</button>
    </p>

</section>   
    
<!-- end of pricing -->
<!--     **************************Section pricing End********************** -->

<!--     **************************Section pokesection Start********************** -->
<section id="pokesection">

    <div class="social_overlay">
        <div class="container">
            <h4 class="text-center"><?php echo eto_get_option($prefix . 'poke_section_1'); ?></h4>

            <div class="row">
                <div class="col-xs-4">
                    <a href="<?php echo eto_get_option($prefix . 'poke_section_2_link'); ?>">
                        <div class="poke_fb"></div>
                        <div
                            class="hidden-sm hidden-xs"><?php echo eto_get_option($prefix . 'poke_section_2'); ?></div>
                    </a>
                </div>
                <div class="col-xs-4">
                    <a href="<?php echo eto_get_option($prefix . 'poke_section_3_link'); ?>">
                        <div class="poke_tw"></div>
                        <div
                            class="hidden-sm hidden-xs"><?php echo eto_get_option($prefix . 'poke_section_3'); ?></div>
                    </a>
                </div>
                <div class="col-xs-4">
                    <a href="<?php echo eto_get_option($prefix . 'poke_section_4_link'); ?>">
                        <div class="poke_li"></div>
                        <div
                            class="hidden-sm hidden-xs"><?php echo eto_get_option($prefix . 'poke_section_4'); ?></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section><!-- end of pokesection -->
<!--     **************************Section pokesection End********************** -->
<!--     **************************Section blog Start************************ -->
<section id="blog">
    <a href="<?php echo get_the_permalink('139'); ?>" target="_blank"> <h2 class="text-center"><?php echo eto_get_option($prefix . 'blog_section_1'); ?></h2></a>

    <div class="container">

        <div class="row">
            <ul id="flexiselDemo2">
                <?php
                $args = array(
                    //'post_type' => 'success-story',
                    'order' => 'DESC',
                    'orderby' => 'date',
                        //'posts_per_page' => 12
                );
                $loop = new WP_Query($args);
                while ($loop->have_posts()) : $loop->the_post();
                    $image1_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                    if (class_exists('Dynamic_Featured_Image')) {
                        global $dynamic_featured_image;
                        $featured_images = $dynamic_featured_image->get_featured_images();
                    }
                    ?>
                    <li>
                        <div class="thumbnail">
                            <div class="thumb-contain text-left">
    <?php the_post_thumbnail('blog_slider'); ?>
                                <!--<img src="<?php //echo $image1_url;   ?>" class="img-responsive" alt="blog1">-->

                                <div class="caption">
                                    <h3><a href="<?php echo the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
                                    <small><?php echo get_the_date(); ?></small>
    <?php echo the_excerpt(); ?>
                                    <!--<a href="<?php //echo get_permalink(139);   ?>"  target="_blank" class="btn btn-primary"
                                       role="button">Read More</a>-->
                                    <a href="<?php echo get_permalink(); ?>"  target="_blank" class="btn btn-primary"
                                       role="button">Read More</a>   
                                </div>
                            </div>
                        </div>
                    </li>
<?php endwhile; ?>
            </ul>
        </div>

    </div>
</section><!-- end of blog -->
<!--     **************************Section blog End*********************** -->
<!--     **************************Section get in touch Start********************** -->
<section id="poke">

    <div id="contact" class="text-center container">
        <h2 class="text-center">GET IN TOUCH</h2>

        <p class="text-center">We’re ready to help you crush your quota.</p>
<?php echo do_shortcode('[contact-form-7 id="135" title="Contact form 1"]') ?>
    </div>

</section><!-- end of get in touch -->
<!--     **************************Section get in touch End********************** -->
<input type="hidden" id="plan" name="plan" value=""/>

<div class="modal fade" id="signInToday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-social-login">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Sign In</h4>
            </div>
            <form method="POST" action="<?php echo $sub_domain_url; ?>/signinmaindomain" accept-charset="UTF-8">
                <input name="_token" type="hidden" value="jbCWNjoCUlmm4CGWA5L4xhS6H9GYEuWXFRM6A0te">
                <input type="hidden" id="planId" name="planId" value=""/>

                <div class="modal-body">

                    <div class="main">
                        <div class="loginmodes">

                            <table style="vertical-align:middle;text-align: center;">
                                <tr>
                                    <td rowspan="2" align="center">
                                        <div style="text-align:center;width:665px;">
                                            <a class="btn btn-social btn-lg btn-linkedin" onclick="loginLinkedin();" href="#">
                                                <i class="fa fa-linkedin"></i> Sign in with LinkedIn
                                            </a>
                                        </div>
                                    </td>
                                    <!--<td style="width:316px;">
                                        <label for="email">Email</label> : <input class="form-control" name="email" type="text" value="" id="email">
                                    </td>-->
                                </tr>
                            </table>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function loginLinkedin() {
        var plan = document.getElementById("plan").value;
        plan = plan.toLowerCase();
        var url = 'https://login.abc.com/signup/linkedin/?plan=' + plan;
		//var url = 'http://local.abc.com/signup/linkedin/?plan=' + plan;
        document.location = url;
    }
</script>
<!------------ modal -------------------------->
<div class="error-container"><?php echo $_GET['error']; ?></div>
<div class="success-container"><?php echo $_GET['success']; ?></div>

<div class="modal fade" id="signUpToday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-social-signup">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Sign Up Today</h4>
            </div>
            <div class="modal-body">

                <div class="main">
                    <div class="loginmodes">
                        <ul>
                            <li class="facebook"><a href="{{route('hybridauth',array('social'=>'facebook'))}}" title="Facebook" class="btn btn-primary btn-lg">Facebook</a></li>
                            <li class="google"><a href="{{route('hybridauth',array('social'=>'google'))}}" title="Google" class="btn btn-primary btn-lg">Google</a></li>
                            <li class="linkedin"><a href="{{route('hybridauth',array('social'=>'linkedin'))}}" title="LinkedIn" class="btn btn-primary btn-lg">LinkedIn</a></li>
                            <li class="twitter"><a href="{{route('hybridauth',array('social'=>'twitter'))}}" title="Twitter" class="btn btn-primary btn-lg">Twitter</a></li>
                        </ul>
                    </div>          
                </div>          

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="recoverInToday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-social-signup">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Recover Your Password!</h4>
            </div>
            <div class="modal-body">
                <div class="main">
                    <div class="loginmodes" style="text-align: center;">
                        <table style="vertical-align:middle;margin:auto;">
                            <tr><td style="vertical-align:middle;line-height:46px;color:white;font-size: 20px;">Email : </td>
                                <td>
                                    <form method="POST" action="<?php echo $sub_domain_url; ?>/user/signup/recover" accept-charset="UTF-8">                            
                                        <input class="form-control" name="email" type="text" value="" id="email">                            
                                    </form>
                                </td>
                                <td>
                                    <button id="recoverinForm" class="btn btn-primary btn-lg">Recover!</button>
                                </td></tr>
                        </table>
                    </div>          
                </div>          
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="successAlert" id-label="" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog alert alert-success">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="loginmodes"></div>
    </div>
</div>

<div class="modal fade" id="errorAlert" id-label="" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog alert alert-danger">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="loginmodes"></div>
    </div>
</div>
<?php get_footer(); ?>
