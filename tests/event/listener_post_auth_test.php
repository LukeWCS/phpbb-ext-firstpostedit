<?php
/**
 *
 * @package firstpostedit
 * @copyright (c) 2015 gn#36
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\firstpostedit\tests\event;

class listener_post_auth_test extends listener_base
{
	public static function post_auth_data()
	{
		$acl_get_map = parent::get_auth_base_data();

		$event_data_base = array(
			'post_id' => 1,
			'topic_id' => 1,
			'forum_id' => 1,
			'draft_id' => 1,
			'lastclick' => time(),
			'submit' => false,
			'preview' => false,
			'save' => false,
			'load' => false,
			'refresh' => true,
			'mode' => 'edit',
			'error' => array(),
			'is_authed' => true,
		);

		$event_datasets = array();
		$event_datasets['f20_true'] = $event_data_base;
		$event_datasets['f20_true']['forum_id'] = 20;
		$event_datasets['f20_false'] = $event_datasets['f20_true'];
		$event_datasets['f20_false']['is_authed'] = false;
		$event_datasets['f1_true'] = $event_data_base;
		$event_datasets['f1_false'] = $event_datasets['f1_true'];
		$event_datasets['f1_false']['is_authed'] = false;

		return array(
			// f20 expect true
			array($acl_get_map['all'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['all'], $event_datasets['f20_false'], $event_datasets['f20_true']),
			array($acl_get_map['all_first'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['all_first'], $event_datasets['f20_false'], $event_datasets['f20_true']),
			array($acl_get_map['edit_first'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['edit_first'], $event_datasets['f20_false'], $event_datasets['f20_true']),
			array($acl_get_map['time_first'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['all_reply'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['edit_reply'], $event_datasets['f20_true'], $event_datasets['f20_true']),
			array($acl_get_map['time_reply'], $event_datasets['f20_true'], $event_datasets['f20_true']),

			// f20 expect false
			array($acl_get_map['time_first'], $event_datasets['f20_false'], $event_datasets['f20_false']),
			array($acl_get_map['all_reply'], $event_datasets['f20_false'], $event_datasets['f20_false']),
			array($acl_get_map['edit_reply'], $event_datasets['f20_false'], $event_datasets['f20_false']),
			array($acl_get_map['time_reply'], $event_datasets['f20_false'], $event_datasets['f20_false']),
			array($acl_get_map['none'], $event_datasets['f20_false'], $event_datasets['f20_false']),

			// f1 expect false unless input is true
			array($acl_get_map['all'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['all'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['all_first'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['all_first'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['edit_first'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['edit_first'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['time_first'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['time_first'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['all_reply'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['all_reply'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['edit_reply'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['edit_reply'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['time_reply'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['time_reply'], $event_datasets['f1_false'], $event_datasets['f1_false']),
			array($acl_get_map['none'], $event_datasets['f1_true'], $event_datasets['f1_true']),
			array($acl_get_map['none'], $event_datasets['f1_false'], $event_datasets['f1_false']),
		);

	}

	/**
	 * @dataProvider post_auth_data
	 */
	public function test_post_auth($auth_data, $event, $expected_result)
	{
		// Modify auth
		$this->auth->expects($this->any())
		->method('acl_get')
		->with($this->stringContains('_'), $this->anything())
		->will($this->returnValueMap($auth_data));

		// fetch listener
		$listener = $this->get_listener();

		// Dispatch
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('gn36.def_listen', array($listener, 'post_auth'));
		$dispatcher->dispatch('gn36.def_listen', $event);

		// Check
		$this->assertEquals($expected_result, $event);

	}
}