<?php

namespace Tests\Unit;

use App\Services\XMService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class XMServiceTest extends TestCase
{

    /**
     * @var XMService
     */
    private $xmService;

    /**
     * @var Response
     */
    private $response;

    private $nasdaqListUrl;

    protected function setUp(): void
    {
        $this->nasdaqListUrl = 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';
        $this->xmService = new XMService();
        $this->response = $this->createMock(Response::class);
    }
    
    public function testGetCompanySymbols()
    {
        $json = [
            [
                'Company Name' => 'Mock Company',
                'Financial Status' => 'Mock Financial',
                'Market Category' => 'Mock Category',
                'Symbol' => 'Mock symbol'
            ]
        ];
        Http::shouldReceive('get')
            ->once()
            ->with($this->nasdaqListUrl)
            ->andReturn($this->response);
        $this->response->expects($this->once())
            ->method('ok')
            ->willReturn(true);
        $this->response->expects($this->once())
            ->method('json')
            ->willReturn($json);

        $companySymbol = $this->xmService->getCompanySymbols();
        $this->assertSame($json, $companySymbol);
    }

    public function testGetCompanySymbolsReturnNullWhenResponseNotOk()
    {
        Http::shouldReceive('get')
            ->once()
            ->with($this->nasdaqListUrl)
            ->andReturn($this->response);
        $this->response->expects($this->once())
            ->method('ok')
            ->willReturn(false);
        $this->response->expects($this->exactly(0))
            ->method('json');

        $companySymbol = $this->xmService->getCompanySymbols();
        $this->assertNull($companySymbol);
    }

    public function testGetSymbolDetailsReturnsWhenSymbolExist(){
        $symbol = 'Mock symbol';
        $json = [
            [
                'Company Name' => 'Mock Company',
                'Financial Status' => 'Mock Financial',
                'Market Category' => 'Mock Category',
                'Symbol' => $symbol
            ]
        ];
        Http::shouldReceive('get')
            ->once()
            ->with($this->nasdaqListUrl)
            ->andReturn($this->response);
        $this->response->expects($this->once())
            ->method('ok')
            ->willReturn(true);
        $this->response->expects($this->once())
            ->method('json')
            ->willReturn($json);
        
        $symbolDetails = $this->xmService->getSymbolDetails($symbol);
        $this->assertEquals($json[0], $symbolDetails);
    }

    public function testGetSymbolDetailsReturnsNullWhenSymbolDoesNotExist(){
        $symbol = 'Invalid symbol';
        $json = [
            [
                'Company Name' => 'Mock Company',
                'Financial Status' => 'Mock Financial',
                'Market Category' => 'Mock Category',
                'Symbol' => 'Mock Symbol'
            ]
        ];
        Http::shouldReceive('get')
            ->once()
            ->with($this->nasdaqListUrl)
            ->andReturn($this->response);
        $this->response->expects($this->once())
            ->method('ok')
            ->willReturn(true);
        $this->response->expects($this->once())
            ->method('json')
            ->willReturn($json);
        
        $symbolDetails = $this->xmService->getSymbolDetails($symbol);
        $this->assertNull($symbolDetails);
    }

    
}
