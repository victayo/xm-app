import React from "react";
import moment from "moment";

class HistoricalQuotes extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let tableRows = [];
        let data = this.props.historicalData;
        data.forEach((hd, index) => {
            tableRows.push(
                <tr key={index}>
                    <td>{moment(+hd.date * 1000).format("YYYY-MM-DD")}</td>
                    <td>{hd.open}</td>
                    <td>{hd.high}</td>
                    <td>{hd.low}</td>
                    <td>{hd.close}</td>
                    <td>{hd.Volume}</td>
                </tr>
            );
        });
        return (
            <>
                {data.length > 0 && ( 
                    <div className="card">
                        <div className="card-bodytable-responsive">
                            <div className="card-title text-center m-3">
                                <h3>Historical Data From {this.props.startDate} to {this.props.endDate}</h3>
                            </div>
                            <table className="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Open</th>
                                        <th>High</th>
                                        <th>Low</th>
                                        <th>Close</th>
                                        <th>Volume</th>
                                    </tr>
                                </thead>
                                <tbody>{tableRows}</tbody>
                            </table>
                        </div>
                    </div>

                )}

                {data.length == 0 && (<div className="text-center">No data to display</div>)}
            </>
        );
    }
}

export default HistoricalQuotes;
