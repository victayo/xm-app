import React from "react";
import ReactDOM from "react-dom";
import moment from "moment";
import FusionCharts from "fusioncharts";
import charts from "fusioncharts/fusioncharts.charts";
import ReactFusioncharts from "react-fusioncharts";
import { getData } from "./misc";
import Symbol from "./Symbol";
import HistoricalQuotes from "./HistoricalQuotes";

class XM extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            historicalData: [],
            dataSource: {},
            loading: true,
            startDate: '',
            endDate: '',
            formSet: false
        };
    }

    fetchHistoricalData = (event) => {
        this.setState({
            formSet: true
        });
        let startDate = event.startDate;
        let endDate = event.endDate;
        let unixStartDate = moment(startDate).unix();
        let unixEndDate = moment(endDate).unix();
        let symbol = event.symbol;
        getData(
            `https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data?symbol=${symbol}&region=US`,
            {
                "X-RapidAPI-Key":
                    "5eb3d06a1dmsha5d264099c82260p140997jsn129b7c02ee89",
                "X-RapidAPI-Host": "yh-finance.p.rapidapi.com",
            }
        ).then((data) => {
            let d = data.prices.filter((price) => {
                return price.date >= unixStartDate && price.date <= unixEndDate;
            });
            let dataSource = this.getChartDataSource(d, startDate, endDate);
            this.setState({
                historicalData: d,
                dataSource: dataSource,
                loading: false,
                startDate: startDate,
                endDate: endDate,
            });
        });
    };

    getChartDataSource = (data, startDate, endDate) => {
        let label = [];
        let open = [];
        let close = [];
        data.forEach((row) => {
            let date = moment(row.date * 1000).format("YYYY-MM-DD");
            label.push({ label: date });
            open.push({ value: row.open });
            close.push({ value: row.close });
        });
        return {
            chart: {
                caption: "Open and Close Prices",
                subcaption: `From ${startDate} to ${endDate}`,
                showhovereffect: "1",
                drawcrossline: "1",
                theme: "fusion",
            },
            categories: [
                {
                    category: label,
                },
            ],
            dataset: [
                {
                    seriesname: "Open",
                    data: open,
                },
                {
                    seriesname: "Close",
                    data: close,
                },
            ],
        };
    };

    render() { 
        charts(FusionCharts);
        return (
            <div className="container">
                <div className="mb-4">
                    <Symbol onSubmit={this.fetchHistoricalData} />
                </div>

                {this.state.loading && this.state.formSet && (
                    <div className="card">
                        <div className="card-body">
                            <div className="text-center">
                                <strong>
                                    Fetching historical data. Please wait...
                                </strong>
                            </div>
                        </div>
                    </div>
                )}
 
                {this.state.historicalData.length > 0 && !this.state.loading && (
                    <>
                        <div className="mb-4">
                            <HistoricalQuotes
                                historicalData={this.state.historicalData}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                            ></HistoricalQuotes>
                        </div>
                        <div className="">
                            <ReactFusioncharts
                                type="msline"
                                width="100%"
                                height="100%"
                                dataFormat="JSON"
                                dataSource={this.state.dataSource}
                            />
                        </div>
                    </>
                )}

                {this.state.historicalData.length == 0 && !this.state.loading && (
                    <div className="card">
                        <div className="card-body">
                            <div className="text-center">
                                <strong>
                                    No data to display for this duration
                                </strong>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        );
    }
}

export default XM;

if (document.getElementById("xm-app")) {
    ReactDOM.render(<XM />, document.getElementById("xm-app"));
}
