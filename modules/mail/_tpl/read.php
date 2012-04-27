<p>
	<a href="<?php echo Vars::$MODULE_URI ?>?act=messages&amp;id=<?php echo $this->user_id ?>"><?php echo lng( 'answer' ) ?></a>
</p>
<div class="phdr"><?php echo $this->pref ?>: <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $this->user_id ?>"><?php echo $this->contact_login ?></a> (<?php echo $this->time_message ?>)</div>
<div class="list1">
<?php echo $this->text ?>
		<?php if($this->file): ?>
     <div class="func">
		 <?php echo lng( 'file' ) ?>: <?php echo $this->file ?>
     </div>
		<? endif ?>
</div>
<div class="phdr"><a href="<?php echo Vars::$MODULE_URI ?>?act=<?php echo $this->back ?>"><?php echo lng( 'back' ) ?></a></div>
<p>
		<div class="func">
			<a href="<?php echo Vars::$MODULE_URI ?>?act=send&amp;id=<?php echo Vars::$ID ?>">Переслать</a><br />
			<?php if ( $this->read == 0 && $this->users_id == Vars::$USER_ID ): ?>
			<a href="<?php echo Vars::$MODULE_URI ?>?act=messages&amp;mod=edit&amp;id=<?php echo Vars::$ID ?>"><?php echo lng('edit') ?></a><br />
			<? endif ?>
			<a href="<?php echo Vars::$MODULE_URI ?>?act=messages&amp;mod=delete&amp;id=<?php echo Vars::$ID ?>"><?php echo lng('delete') ?></a><br />
		</div>
	</p>
<p>
<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?php echo lng( 'contacts' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a>
</p>