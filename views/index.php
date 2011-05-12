<?=form_for($f, $blog, 'blog/edit/'.$blog->id)?>
	<div class="field">
		<?=$f->label('name')?>
		<?=$f->text_field('name')?>
	</div>
	<div class="field">
		<?=$f->label('slug')?>
		<?=$f->select('slug', array('test', 'test'))?>
	</div>
	<div class="action">
		<?=submit_tag()?>
	</div>
<?=form_end()?>