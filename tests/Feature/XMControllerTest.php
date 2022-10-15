<?php

namespace Tests\Unit;

use App\Http\Controllers\XMController;
use App\Services\XMService;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
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
        $this->validator = $this->getMockBuilder(ValidationValidator::class)->getMock();
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
}
