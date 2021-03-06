<?php

namespace UnitTests\POData\OperationContext\ZF1;


use POData\Common\ODataConstants;
use POData\OperationContext\HTTPRequestMethod;
use POData\OperationContext\IHTTPRequest;
use POData\OperationContext\ZF1\ZF1Request;
use Phockito;


class ZF1RequestTest extends \PHPUnit_Framework_TestCase
{

	/** @var  \Zend_Controller_Request_Http */
	protected $mockRequest;

	public function setUp()
	{
		Phockito::include_hamcrest();
		$this->mockRequest = Phockito::mock('Zend_Controller_Request_Http');
	}

	public function testConstructor(){

		$req = new ZF1Request($this->mockRequest );

		$this->assertTrue($req instanceof IHTTPRequest);

	}

	public function testGetMethod()
	{
		Phockito::when($this->mockRequest->getMethod())
			->return("GET");

		$req = new ZF1Request($this->mockRequest );

		$actual = $req->getMethod();

		$expected = HTTPRequestMethod::GET();

		$this->assertEquals($expected, $actual);
	}


	public function testGetMethodUnknown()
	{
		Phockito::when($this->mockRequest->getMethod())
			->return("UNKOWN");

		$req = new ZF1Request($this->mockRequest );

		try{
			$req->getMethod();
			$this->fail("Expected exception not thrown");
		}
		catch(\Exception $ex) {
			$this->assertEquals("Value 'UNKOWN' is not part of the enum POData\OperationContext\HTTPRequestMethod", $ex->getMessage());
		}

	}

	public function testGetRawUrl()
	{
		$fakeScheme = "http";
		$fakeHost = "somehost";
		$fakePath = "/SomeUrl/SomePath/" . uniqid();

		Phockito::when($this->mockRequest->getRequestUri())
			->return($fakePath);

		Phockito::when($this->mockRequest->getScheme())
			->return($fakeScheme);

		Phockito::when($this->mockRequest->getHttpHost())
			->return($fakeHost);

		$req = new ZF1Request($this->mockRequest );

		$expected = "$fakeScheme://$fakeHost$fakePath";


		$actual = $req->getRawUrl();


		$this->assertSame($expected, $actual);
	}


	public function testGetHeader()
	{

		$header = "SomeWhackyHeader" . uniqid();

		$expected = "SomeHeaderValue" . uniqid();

		Phockito::when($this->mockRequest->getHeader($header))
			->return($expected);

		$req = new ZF1Request($this->mockRequest );


		$actual = $req->getRequestHeader($header);



		$this->assertSame($expected, $actual);
	}


	public function testGetHeaderNonExistent()
	{
		$nonExistentHeader = "SomeWhackyHeader" . uniqid();

		//Zend returns false when a header isn't present
		Phockito::when($this->mockRequest->getHeader($nonExistentHeader))
			->return(false);

		$req = new ZF1Request($this->mockRequest );

		$actual = $req->getRequestHeader($nonExistentHeader);

		$this->assertNull($actual);
	}

	public function testGetQueryParameters()
	{

		$fakeParams = array(
			'$format' => 'json',
			'$filter' => "Property eq '1'",
		);

		//This may be the wrong thing to mock..see notes in implementation
		Phockito::when($this->mockRequest->getParams())
			->return($fakeParams);

		$req = new ZF1Request($this->mockRequest );

		$actual = $req->getQueryParameters();

		$expected = array(
			array('$format' => 'json' ),
			array('$filter' => "Property eq '1'" ),
		);

		$this->assertSame($expected, $actual);
	}



}