<?php

include_once('Utils.php');
include_once('User.php');

class Index {

	private $user;
	private $utils;

	function __construct() {
		$this->user = new User();
		$this->utils = new Utils();

		$this->utils->htmlFragmentStart('Terrific Start');
		$this->htmlList();
		$this->utils->htmlFragmentEnd('');
	}

	private function htmlList() {
		?>
		<h1>Terrific Start.
			<span>Hi <?php echo $this->user->getName(); ?>.</span>
		</h1>

		<div class="row">
			<div class="col-md-6">
				<h3>Views</h3>
				<?php
				global $config;

				// views
				$files = glob(BASE.$config->micro->view_directory.'/*.'.$config->micro->view_file_extension);
				$this->viewList($files);

				// views in subfolders
				foreach ( glob( BASE . $config->micro->view_directory.'/*', GLOB_ONLYDIR ) as $dir ) {
					$base_dir = basename($dir);
					if ($base_dir !== basename($config->micro->view_partials_directory)) {
						$files = glob($dir.'/*.'.$config->micro->view_file_extension);
						$this->viewList($files, $base_dir);
					}
				}
				?>
			</div>
			<div class="col-md-6">
				<h3>Tools</h3>
				<div class="list-group">
					<?php
					global $config;
					foreach ( $config->micro->components as $key => $component ) {
						echo '<a href="' . TERRIFICURL . 'create/' . $key . '" class="list-group-item">';
						echo 'Create ' . ucfirst($key);
						echo '</a>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	private function viewList($files, $dir = NULL) {
		global $config;
		?>
		<div class="list-group">
		<?php
		foreach($files as $file){
			if(basename($file, '.'.$config->micro->view_file_extension) !== basename(__FILE__, '.'.$config->micro->view_file_extension)){
				?>
				<a href="<?php echo BASEURL, !empty($dir) ? $dir.'-' : '', basename($file, '.'.$config->micro->view_file_extension); ?>" class="list-group-item">
					<?php echo !empty($dir) ? ucwords($dir).' ' : '', ucwords(str_replace('-', ' ', basename($file, '.'.$config->micro->view_file_extension))); ?>
				</a>
			<?php
			}
		}
		?>
		</div>
		<?php
	}
}
