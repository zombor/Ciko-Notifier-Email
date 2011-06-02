<?php
/**
 * View file for the index
 *
 * @package    Ciko
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Ciko/raw/develop/LICENSE
 */
class View_Ciko_Email_Failure extends Kostache
{
	public $project;

	/**
	 * Var method to list all projects
	 *
	 * @return array
	 */
	public function project_name()
	{
		return $this->project->name();
	}
}