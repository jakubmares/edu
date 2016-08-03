<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

/**
 * Description of StaticPageManager
 *
 * @author jakubmares
 */
class StaticPageManager extends BaseManager{
	const 
			TABLE_NAME = 'static_page',
			COLUMN_ID = 'id',
			COLUMN_TITLE = 'title',
			COLUMN_CONTENT = 'content',
			COLUMN_USER_ID = 'user_id',
			COLUMN_UPDATED_AT = 'updated_at';
	
	
	
}
