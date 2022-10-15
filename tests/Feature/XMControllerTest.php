<?php

namespace Tests\Unit;

use App\Http\Controllers\XMController;
use App\Services\XMService;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class XMControllerTest extends TestCase
{

    /**
     * @var XMService
     */
    private $xmService;

    /**
     * @var validationValidator
     */
    private $validator;

    private $request;

    protected function setUp(): void{
        parent::setUp();

        $this->xmService = $this->createMock(XMService::class);
        $this->validator = $this->createMock(ValidationValidator::class);
        $this->request = $this->createMock(Request::class); 
    }
    /**
     *
     * @return void
     */
    public function testSubmitWhenPostDataIsValid()
    {
        /**
         * Data sent from the front end
         */
        $requestData = [
            'symbol' => 'GOOG',
            'startDate' => '2022-10-13',
            'endDate' => '2022-10-14',
            'email' => 'validtest@gmail.com'
        ];

        /**
         * validation rules
         */
        $rules = [
            'email' => 'required|email:rfc,dns',
            'startDate' => 'required|date|before_or_equal:endDate|before_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'symbol' => 'required'
        ];

        /**
         * Company details of symbols
         */
        $companyDetails = [
            'Company Name' => 'Mock Company',
            'Financial Status' => 'Mock Financial',
            'Market Category' => 'Mock Category',
            'Symbol' => 'Mock symbol'
        ];

        $startDate = $requestData['startDate'];
        $endDate = $requestData['endDate'];
        $symbol = $requestData['symbol'];

        $this->xmService->expects($this->once())
        ->method('getSymbolDetails')
        ->with($symbol)
        ->willReturn($companyDetails);

        $this->xmService->expects($this->once())
        ->method('sendMail')
        ->with($startDate, $endDate, $companyDetails['Company Name'])
        ->willReturn(
            [
                'Company Name' => 'Mock Company',
                'Financial Status' => 'Mock Financial',
                'Market Category' => 'Mock Category',
                'Symbol' => 'Mock symbol'
            ]);

        $this->request->expects($this->once())
        ->method('all')
        ->willReturn($requestData);

        $this->request->expects($this->exactly(3))->method('get')
        ->withConsecutive(
            ['symbol'], ['startDate'], ['endDate']
        )->willReturnOnConsecutiveCalls($symbol, $startDate, $endDate);

        Validator::shouldReceive('make')->with($requestData, $rules)->andReturn($this->validator);
        $this->validator->expects($this->once())
        ->method('fails')
        ->willReturn(false);

        $controller = new XMController($this->xmService);
        $response = $controller->submit($this->request);
        $data = $response->getData();
        $this->assertTrue($response->status() == 200);
        $this->assertTrue($data->success);
        $this->assertTrue(empty($data->messages));
    }

    public function testSubmitWhenPostDataIsInvalid()
    {
        /**
         * Data sent from the front end
         */
        $requestData = [
            'symbol' => 'GOOG',
            'startDate' => '2022-10-13',
            'endDate' => '2022-10-14',
            'email' => 'invalid@'
        ];

        /**
         * validation rules
         */
        $rules = [
            'email' => 'required|email:rfc,dns',
            'startDate' => 'required|date|before_or_equal:endDate|before_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'symbol' => 'required'
        ];

        /**
         * Company details of symbols
         */
        $companyDetails = [
            'Company Name' => 'Mock Company',
            'Financial Status' => 'Mock Financial',
            'Market Category' => 'Mock Category',
            'Symbol' => 'Mock symbol'
        ];

        $startDate = $requestData['startDate'];
        $endDate = $requestData['endDate'];
        $symbol = $requestData['symbol'];

        $this->xmService->expects($this->exactly(0))
        ->method('getSymbolDetails');

        $this->xmService->expects($this->exactly(0))
        ->method('sendMail');

        $this->request->expects($this->once())
        ->method('all')
        ->willReturn($requestData);

        $this->request->expects($this->exactly(0))->method('get');

        Validator::shouldReceive('make')->with($requestData, $rules)->andReturn($this->validator);

        $this->validator->expects($this->once())
        ->method('fails')
        ->willReturn(true);

        $this->validator->expects($this->once())
        ->method('messages')
        ->willReturn(['email' => 'Email is invalid']);

        $controller = new XMController($this->xmService);
        $response = $controller->submit($this->request);
        $data = $response->getData();
        $this->assertTrue($response->status() == 200);
        $this->assertFalse($data->success);
        $this->assertFalse(empty($data->messages));
    }

    public function testSubmitWhenSymbolIsNoFound()
    {
        /**
         * Data sent from the front end
         */
        $requestData = [
            'symbol' => 'GOOG',
            'startDate' => '2022-10-13',
            'endDate' => '2022-10-14',
            'email' => 'valid@gmail.com'
        ];

        /**
         * validation rules
         */
        $rules = [
            'email' => 'required|email:rfc,dns',
            'startDate' => 'required|date|before_or_equal:endDate|before_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'symbol' => 'required'
        ];

        /**
         * Company details of symbols
         */
        $companyDetails = [
            'Company Name' => 'Mock Company',
            'Financial Status' => 'Mock Financial',
            'Market Category' => 'Mock Category',
            'Symbol' => 'Mock symbol'
        ];

        $startDate = $requestData['startDate'];
        $endDate = $requestData['endDate'];
        $symbol = $requestData['symbol'];

        $this->xmService->expects($this->once())
        ->method('getSymbolDetails')
        ->with($symbol)
        ->willReturn(null);

        $this->xmService->expects($this->exactly(0))
        ->method('sendMail');

        $this->request->expects($this->once())
        ->method('all')
        ->willReturn($requestData);

        $this->request->expects($this->once())
        ->method('get')
        ->with('symbol')
        ->willReturn($symbol);

        Validator::shouldReceive('make')->with($requestData, $rules)->andReturn($this->validator);

        $this->validator->expects($this->once())
        ->method('fails')
        ->willReturn(false);

        $this->validator->expects($this->exactly(0))
        ->method('messages');

        $controller = new XMController($this->xmService);
        $response = $controller->submit($this->request);
        $data = $response->getData();
        $this->assertTrue($response->status() == 200);
        $this->assertFalse($data->success);
        $this->assertFalse(empty($data->messages));
    }
}
