<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Interfaces\RendersTemplate;
use Fortuneglobe\IceHawk\Responses\TemplatePage;

class TemplatePageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @runInSeparateProcess
	 */
	public function testRespondsHtmlWithData()
	{
		$data = [ 'unit', 'test' ];

		$templateEngine = $this->getMockBuilder( RendersTemplate::class )
		                       ->setMethods( [ 'renderWithData' ] )
		                       ->getMock();

		$templateEngine->expects( $this->once() )
		               ->method( 'renderWithData' )
		               ->willReturnCallback(
			               function ( $template, array $data )
			               {
				               return $template . ' - ' . join( "\n", $data );
			               }
		               );

		( new TemplatePage( '/Unit/Test.tpl', $data, 'utf-8', $templateEngine ) )->respond();

		$this->assertContains( 'Content-Type: text/html; charset=utf-8', xdebug_get_headers() );
		$this->expectOutputString( "/Unit/Test.tpl - unit\ntest" );
	}
}
