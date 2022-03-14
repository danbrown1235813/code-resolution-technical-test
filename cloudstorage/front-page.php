<?php get_header(); ?>

<?php get_template_part('sidebar'); ?>
		
		<!-- Body -->
		<div class="body animate__animated animate__fadeIn">
			
			<h3 class="site-title">Subscription for <span class="colour-primary">Cloud Storage</span></h3>

			<div class="main">

				<!-- Banner -->
				<div class="banner">
					<img src="<?php echo get_template_directory_uri() . '/images/banner.png'; ?>" alt="">
				</div>

				<!-- Content -->
				<div class="content">
					<h1><?php echo get_field('title'); ?></h1>
					<?php echo get_field('description'); ?>
				</div>
					
				<!-- Product Table -->
				<div class="product-table">
					<div class="product-table__header">

						<h3>Select Below</h3>

						<!-- Settings -->
						<div class="settings">

							<!-- Hidden Data -->
							<!-- Remember current settings -->
							<div id="settings-data" data-in-stock="" data-order-by=""></div>

							<!-- Filter - In Stock / All  -->
							<div class="filter toggle_in_stock">
								<a><img src="<?php echo get_template_directory_uri() . '/images/ellipse.svg'; ?>" alt=""><span>Showing All</span></a>
							</div>

							<!-- Order By -->
							<div class="order-by">
								<a><span>Order By</span> <span class="order-by__current"></span> <img src="<?php echo get_template_directory_uri() . '/images/order-arrow.svg'; ?>" alt=""></a>
								<div class="order-by__options">
									<a class="sort_lowest" href="">Lowest Price</a>
									<a class="sort_highest" href="">Highest Price</a>
								</div>
							</div>
						</div>
					</div>

					<!-- Product List -->
					<div class="products">

						<!-- Loading Spinner -->
						<div class="loading">
							<div class="lds-roller">
								<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
							</div>
						</div>
						
						<?php // Returned HTML example - 1 product 
						/*
						<div class="product">
							<img class="product__image" src="<?php echo get_template_directory_uri() . '/images/product-image.svg'; ?>" alt="">
							<div class="product__body">

								<div class="product__info">
									<h3>Starter</h3>
									<p>20 GB</p>
								</div>

								<div class="product__cost">
									<p>$9.99/pm</p>
								</div>

								<div class="product__select">
									<a class="product__select-btn"><span>Select</span> <img src="<?php echo get_template_directory_uri() . '/images/select-icon.svg'; ?>" alt=""></a>
								</div>
									
							</div>
						</div>
						*/ ?>

					</div><!--/.products-->
				</div><!--/.table-->

				<!-- Additional -->
				<div class="additional">Need more? <a href="#">Contact us now</a> <img class="link-arrow" src="<?php echo get_template_directory_uri() . '/images/arrow.svg'; ?>" alt="">
				</div>
										
			</div><!--/.main-->
		</div><!--.body-->
	</div><!--/.body-wrap-->

	<?php get_footer(); ?>

	
</body>
</html>