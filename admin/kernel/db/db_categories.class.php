<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar
 
 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class DB_CATEGORIES {

/*
======================================================================================
	VARIABLES
======================================================================================
*/
		public $file_xml; 			// Contains the link to the blog_config.xml file
		public $obj_xml; 				// Contains the object of the blog_config.xml file

/*
======================================================================================
	CONSTRUCTORS
======================================================================================
*/
		function DB_CATEGORIES($file)
		{
			$this->file_xml = $file;

			if (file_exists($this->file_xml))
			{
				$this->obj_xml = new NBXML($this->file_xml, 0, TRUE, '', FALSE);
			}
			else
			{
				return(false);
			}

			return(true);
		}

/*
======================================================================================
	PUBLIC METHODS
======================================================================================
*/
		public function savetofile()
		{
			return( $this->obj_xml->asXML($this->file_xml) );
		}

		public function add($args)
		{
			$tmp_node = $this->obj_xml->xpath('/categories/category[@name="'.$args['name'].'"]');

			if( $tmp_node == array() )
			{
				$new_node = $this->obj_xml->addChild('category','');
				$new_node->addAttribute('id', $this->get_autoinc());
				$new_node->addAttribute('name', $args['name'] );
				$this->set_autoinc(1);
			}
		}

		public function set($args)
		{
			$tmp_node = $this->obj_xml->xpath('/categories/category[@id="'.$args['id'].'"]');

			// Category not found
			if( $tmp_node == array() )
				return(false);

			$tmp_node[0]->attributes()->name	= utf8_encode($args['name']);

			return(true);
		}

		public function delete($args)
		{
			$tmp_node = $this->obj_xml->xpath('/categories/category[@id="'.$args['id'].'"]');

			// Category not found
			if( $tmp_node == array() )
				return(false);

			// Need at least 1 category
			if( $this->get_count() == 1 )
				return(false);

			// Check if the category have some post assoc
			if( $this->get_post_count($args['id']) > 0)
				return(false);

			$dom = dom_import_simplexml($tmp_node[0]);
			$dom->parentNode->removeChild($dom);

			return( $this->savetofile() );
		}

		public function get_all()
		{
			$tmp_array = array();
			foreach( $this->obj_xml->children() as $children )
			{
				$row					= array();
				$row['id']				= (int) $children->attributes()->id;
				$row['name']			= (string) utf8_decode($children->attributes()->name);

				array_push($tmp_array, $row);
			}
			return( $tmp_array );
		}

		public function get_count()
		{
			return( count( $this->obj_xml ) );
		}

		public function get_post_count($id)
		{
			global $_FS;

			return( count($_FS->ls(PATH_POSTS, '*.'.$id.'.*.*.*.*.*.*.*.*', 'xml', false, false, false)) );
		}


		//
		// ///////////////////// TAL VEZ NO SE USAN !!
		//
		public function exist($id)
		{
			return( $this->obj_xml->xpath('/categories/category[@id='.$id.']') != array() );
		}

		public function get($id)
		{
			$tmp_node = $this->obj_xml->xpath('/categories/category[@id="'.$id.'"]');
			$tmp_array = array();

			if( $tmp_node != array() )
			{
				$tmp_array['id'] 				= $id;
				$tmp_array['name'] 			= (string) utf8_decode($tmp_node[0]->attributes()->name);
				$tmp_array['description']	= (string) utf8_decode($tmp_node[0]->description);
			}
			else
			{
				$this->flag_error = true;
				$this->flag_i18n = 'CATEGORY_NOT_FOUND';
			}

			return( $tmp_array );
		}

		public function get_id($name)
		{
			$tmp_node = $this->obj_xml->xpath('/categories/category[@name="'.utf8_encode($name).'"]');

			if( $tmp_node != array() )
			{
				return( (int) $tmp_node[0]->attributes()->id );
			}
			else
			{
				$this->flag_error = true;
				$this->flag_i18n = 'CATEGORY_NOT_FOUND';
				return(-1);
			}
		}

/*
======================================================================================
	PRIVATE METHODS
======================================================================================
*/
		private function get_autoinc()
		{
			return( (int) $this->obj_xml['autoinc'] );
		}

		private function set_autoinc($value = 0)
		{
			$this->obj_xml['autoinc'] = $value + $this->get_autoinc();
		}

} // END Class

?>
