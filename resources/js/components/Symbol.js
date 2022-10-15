import React from 'react';
import DatePicker from "react-datepicker";
import moment from 'moment';
import "react-datepicker/dist/react-datepicker.css";
import Select from 'react-select'
import { getData, postData } from './misc';

class Symbol extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            startDate: new Date(),
            endDate: new Date(),
            symbolData: [],
            options: [],
            symbol: '',
            email: '',
            invalidSymbol: '',
            invalidEmail: '',
            invalidStartDate: '',
            invalidEndDate: '',
            loading: true,
            submitting: false
        }
    }

    componentDidMount(){
        getData("https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json").then(data => {
            let options = [];
            data.forEach(symb => {
                options.push({
                    value: symb.Symbol,
                    label: symb.Symbol
                })
            })
            this.setState({
                symbolData: data,
                options: options,
                loading: false
            })
        })
    }

    setStartDate = (date) => {
        this.setState({
            startDate: date
        })
    }

    setEndDate = (date) => {
        console.log(moment(date).format('YYYY'));
        this.setState({
            endDate: date
        })
    }

    symbolSelected = (event) => {
        this.setState({
            symbol: event.value
        })
    }

    emailChanged = (event) => {
        this.setState({
            email: event.target.value
        })
    }

    submit = (event) => {
        event.preventDefault();
        this.setState({
            invalidSymbol: '',
            invalidEmail: '',
            invalidStartDate: '',
            invalidEndDate: '',
            submitting: true
        });
        let submitData = {
            symbol: this.state.symbol,
            startDate: moment(this.state.startDate).format('YYYY-MM-DD'),
            endDate: moment(this.state.endDate).format('YYYY-MM-DD'),
            email: this.state.email
        }
        postData('/submit', submitData).then(data => {
            if(!data.success){
                let messages = data.messages;
                if('startDate' in messages){
                    this.setState({
                        invalidStartDate: messages['startDate']
                    })
                }
                if('endDate' in messages){
                    this.setState({
                        invalidEndDate: messages['endDate']
                    })
                }
                if('symbol' in messages){
                    this.setState({
                        invalidSymbol: messages['symbol']
                    })
                }
                if('email' in messages){
                    this.setState({
                        invalidEmail: messages['email']
                    })
                }
            }else{
                this.props.onSubmit(submitData);
            }
            this.setState({
                submitting: false
            })
        })
    }

    render(){
        return (<div className='container'>
            <div className='row g3'>
                {this.state.submitting &&
                    <div className="alert alert-info text-center">
                        <strong>Submitting data. Please wait...</strong>
                    </div>
                }
                <div className='card'>
                    <div className='card-body'>
                        <div className='mb-3'>
                            <label  className="form-label">Symbol <span className='text-danger'>*</span></label>
                            <div>                            
                                <Select options={this.state.options} className="is-valid" onChange={this.symbolSelected} required/>
                                {this.state.invalidSymbol.length > 0 && <strong><small className='text-danger'>{this.state.invalidSymbol}</small></strong>}
                            </div>
                        </div>

                        <div className='mb-3'>
                            <label className='form-label'>Start Date <span className='text-danger'>*</span></label>
                            <DatePicker className="form-control" selected={this.state.startDate} onChange={this.setStartDate} dateFormat="yyyy-MM-dd" maxDate = {new Date()} required={true}/>
                            {this.state.invalidStartDate.length > 0 && <strong><small className='text-danger'>{this.state.invalidStartDate}</small></strong>}
                        </div>

                        <div className='mb-3'>
                            <label className='form-label'>End Date <span className='text-danger'>*</span></label>
                            <DatePicker className="form-control" selected={this.state.endDate} onChange={this.setEndDate} dateFormat="yyyy-MM-dd" minDate={this.state.startDate} required={true}/>
                            {this.state.invalidEndDate.length > 0 && <strong><small className='text-danger'>{this.state.invalidEndDate}</small></strong>}
                        </div>

                        <div className='mb-3'>
                            <label  className="form-label">Email Address <span className='text-danger'>*</span></label>
                            <input type="email" className="form-control" placeholder="name@example.com" value={this.state.email} onChange={this.emailChanged} required/>
                            {this.state.invalidEmail.length > 0 && <strong><small className='text-danger'>{this.state.invalidEmail}</small></strong>}
                        </div>
                        <button type="submit" className="btn btn-primary" onClick={this.submit} disabled={this.state.loading || this.state.submitting}>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>);
    }
}

export default Symbol;