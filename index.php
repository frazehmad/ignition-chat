	<?php include('widget/db.inc.php'); ?>
	<?php
		$error_message = '';
		if (isset($_POST['create_widget'])) {
			$email = $_POST['email'];
			
			if ($email == '') {
				$error_message .= 'We need your email address to to create your widget.';
			}
			else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$error_message .= 'Your email address must be in the format of name@domain.com';
			}
			else {
				// check if the email exists
				$stmt = $db->prepare("SELECT * FROM users WHERE email=?");
				$stmt->execute(array($email));
			
				if (!$stmt->rowCount()) {
					$front_password = md5(time().$email.'front');
					$back_password = md5(time().$email.'back');
					$stmt = $db->prepare("INSERT INTO users (email, front_password, back_password, language) VALUES (?,?,?,?)");
					$stmt->execute(array($email, $front_password, $back_password, 'en'));
					$success = 1;
				}
				else {
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$front_password = $row['front_password'];
					$back_password = $row['back_password'];
					$success = 1;
				}
			}
		}
	?>
	<?php include('header.php') ; ?>
	<body>
	
		<!-- HEADER
		============================================= -->
		<header id="header">
		
			<div class="navbar navbar-fixed-top">	
				<div class="container">
				
					<!-- Logo & Responsive Menu -->
					<div class="navbar-header">
						<button type="button" id="nav-toggle" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-menu">
							<span class="sr-only">Toggle navigation</span> 
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" id="GoToHome" href="#intro"><span style="color: rgb(62, 62, 62); font-size: 30px; font-family: &quot;Lato&quot;,sans-serif;">Ignition Chat</span>
<h2 style="font-size: 16px; color: #428bca;">Real-Time Chat &amp; Translation</h2></a>
					</div>	<!-- /.navbar-header -->
					
					<!-- Navigation -->
					<nav id="navigation-menu" class="collapse navbar-collapse"  role="navigation">
					  <ul class="nav navbar-nav navbar-right">
						<li><a id="GoToHome" class="selected-nav" href="#intro">Home</a></li>
						<li><a id="GoToAbout" href="#about-1">About</a></li>
						<li><a id="GoToFaq" href="#faq">FAQ</a></li>
						<li><a id="GoToClients" href="#call-to-action">Demo</a></li>
						<li><a href="mailto: farazahmedmemon@gmail.com,frazehmad@gmail.com">Contact</a></li>
					  </ul>
					</nav>	<!-- /.navbar-collapse -->
					
				</div>	<!-- /.container -->
			</div>	<!-- /.navbar -->
			
		</header>	 <!-- END HEADER -->
	
	
		<!-- CONTENT WRAPPER
		============================================= -->
		<div id="content-wrapper">
		
		
			<!-- INTRO
			============================================= -->
			<section id="intro" class="intro-parallax">
				<div class="container">								
					<div class="row">
										
						<!-- Intro Section Description -->		
						<div id="intro_description" class="col-sm-7 col-md-7">
						
							<!-- Intro Section Title -->
							<h1><strong>Ignition Chat</strong> is the Most <strong>Simple Way</strong> to chat with your customers in real-time <strong>in their own language!</strong></h1>
								
							<!-- Description #1 -->	
							<div class="intro_feature">
								<h4><i class="fa fa-check"></i> Connect with your Customers!</h4>
								<p>It's so important to connect with your international customers and understand their needs and make them feel good about your products and brand, but language can often be a barrier. Ignition Chat helps you eliminate that barrier.</p>
							</div>
							
							<!-- Description #2 -->	
							<div class="intro_feature">
								<h4><i class="fa fa-check"></i>Increase Sales!</h4>
								<p>Ignition Chat helps you close more sales as you will be able to communicate effectively with your customers in the language they can understand.</p>
							</div>
							
							<!-- Description #3 -->	
							<div class="intro_feature">
								<h4><i class="fa fa-check"></i> Reduce Costs!</h4>
								<p>Instead of hiring live support personnel for each of your target nationality, Ignition Chat helps you reduce costs by translating the customers for you.
								</p>
							</div>
								
						</div>	<!-- End Intro Section Description -->	
						
							
						<!-- Intro Section Form -->		
						<div id="intro_form" class="col-sm-5 col-md-5">
						
							<!--Register form -->
							<div class="form_register">
								<h3 style="text-align: center;"> Get Your Own Chat! </h3>
								
								<?php if (isset($success)) { ?>
								<p>
									Backend Widget Code:<br />
									<textarea style="width: 100%;" onclick="this.focus(); this.select();"><script>var _igc = _igc || []; _igc.push(['<?php echo $back_password; ?>']); _igc.push(['b']); (function() {var igc = document.createElement('script'); igc.type = 'text/javascript'; igc.async = true; igc.src = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/igc.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(igc, s); })();</script></textarea>
								</p>
								<div style="background-color: #FEEFB3; padding: 5px; color: #9F6000; margin: -10px 0 10px;">
									<strong>VERY IMPORTANT :</strong><br />
									Place the above code inside the secure area of the your website.
								</div>
								<p>
									Frontend Widget Code:<br />
									<textarea style="width: 100%;"onclick="this.focus(); this.select();"><script>var _igc = _igc || []; _igc.push(['<?php echo $front_password; ?>']); _igc.push(['f']); (function() {var igc = document.createElement('script'); igc.type = 'text/javascript'; igc.async = true; igc.src = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/igc.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(igc, s); })();</script></textarea>
								</p>
								<?php } else { ?>
								<form action="" class="row" method="post">
										
									<div id="input_email" class="col-md-12">
										<input id="email" class="form-control" type="text" name="email" placeholder="Enter Your Email">
										<?php if ($error_message != '') echo '<label for="email" generated="true" class="error">'.$error_message.'</label>'; ?>
									</div>
			
									<!-- Submit Button -->
									<div id="form_register_btn" class="text-center">
										<input class="btn btn-primary btn-lg" type="submit" name="create_widget" value="Go!" id="submit">
									</div>  
								<?php } ?>									
								</form>						
							</div>							
						</div>	<!-- End Intro Section Form -->
					
					</div>	<!-- End row -->	
					
				</div>	   <!-- End container -->		
			</section>  <!-- END INTRO -->
			
			
			<!-- ABOUT-1
			============================================= -->
			<section id="about-1">
				<div class="container">	
				
					<!-- Section Title -->	
					<div class="row">
						<div class="col-md-12 titlebar">
							<h1>About <strong>Ignition Chat</strong></h1>
							<p>Ignition Chat is focused on helping e-commerce stores with effective real-time communication with their international customers.</p>
						</div>
					</div>
				
					<div class="row">
					
						<!--  About-1 Text -->	
						<div id="about-1-text" class="col-md-6">
														
							<!--  Accordion -->
							<div id="accordion_holder">	
								<h4>Why choose <strong>Ignition Chat</strong>?</h4>

								<ul class="accordion clearfix">
									
									<!-- Text #1 -->
									<li id="text_1">
										<a href="#text1">No cost</a>
										<div>
											<p>Yes, you read that right.</p>
										</div>									
									</li>				
											
									<!-- Text #2 -->
									<li id="text_2">
										<a href="#text2">Reliable &amp; Fast</a>								
										<div>
											<p>We use some of the sophisticated technologies which means that you will never lose a customer and all the messages are sent / received within a second.</p>
										</div>									
									</li>
											
									<!-- Text #3 -->
									<li id="text_3">
										<a href="#text3">Quick Installation</a>								
										<div>
											<p>All you need to do is to add a snippet of javascript code and you are done.</p>
										</div>									
									</li>

								</ul>	
								
							</div>	<!--  End Accordion -->
							
						</div>	<!-- End About-1 Text --> 
						
						<!-- About-1 Image --> 
						<div id="about-1-img" class="col-md-6 text-center">
							<img class="img-responsive" src="img/thumbs/startup-1.png" alt="image" />		
						</div>
					
					</div>	<!-- End row -->	
				</div>	   <!-- End container -->		
			</section>  <!-- END ABOUT-1 -->			
			
			<!-- FAQs
			============================================= -->
			<section id="faq">
				<div class="container">	
				
					<!-- Section Title -->	
					<div class="row">
						<div class="col-md-12 titlebar">
							<h1>Frequently <strong>asked questions</strong></h1>
							<p>Please refer below to some commonly asked questions.</p>
						</div>
					</div>
				
					<div class="row">
					
						<!-- Question #1-->
						<div id="question_1" class="col-md-6">	
							<div class="question">
								<h4>Can I chat with multiple customers at the same time?</h4>
								<p>This is not a group chat. You can chat with only one customer at a time per widget.</p>
							</div>							
						</div>
						
						<!-- Question #2-->
						<div id="question_2" class="col-md-6">							
							<div class="question">
								<h4>Will placing the widget on my site affect my site in any way?</h4>
								<p>The widget runs in an iframe and doesn't touch any part of your site.</p>
							</div>
						</div>
						
					</div>	<!-- End row -->						
				
					<div class="row">
					
						<!-- Question #3-->
						<div id="question_3" class="col-md-6">	
							<div class="question">
								<h4>How quickly the messages are sent/received with translation?</h4>
								<p>Within a second in most cases.</p>
							</div>							
						</div>
						
						<!-- Question #4-->
						<div id="question_4" class="col-md-6">							
							<div class="question">
								<h4>Will the widget run with javascript disabled?</h4>
								<p>Javascript is required for the widget to run.</p>
							</div>
						</div>
					
					</div>	<!-- End row -->
					
				</div>	   <!-- End container -->		
			</section>  <!-- END FAQs -->			
			
			<!-- CALL TO ACTION
			============================================= -->
			<section id="call-to-action" class="parallax">
				<div class="container">	
					<div class="row">
					
						<!-- Call To Action Content -->	
						<div class="col-sm-12 text-center">
						
							<h1><strong>DEMO</strong></h1>
							
						</div>	<!-- End Call To Action Content -->	
					
					</div>	<!-- End row -->	
				</div>	   <!-- End container -->		
			</section>  <!-- END CALL TO ACTION -->			
			
			<?php include('footer.php'); ?>		
	
	</body>

</html>