import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Card from '../../components/Card';

const Statistics = () => {
    const [data, setData] = useState([]);
    
    useEffect(() => {
        const fetchData = async () => {
            try {
            const response = await axios.get('api/customer-calls/get-statistics/');
            setData(response.data.result);
            } catch (error) {
            console.error('Error fetching data:', error);
            }
        };

        fetchData();

        setInterval(fetchData, 5000);
        }, []);

    return (
        <React.Fragment>
            <Card>
                <div className="card-header">
                    <span className='fw-semibold'>Statistics</span>
                </div>
                <div className='card-body'>
                    <table className="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col" className='text-center'>Customer ID</th>
                                <th scope="col" className='text-center'>Same Continent - Total Number of Calls</th>
                                <th scope="col" className='text-center'>Same Continent Total Calls Duration</th>
                                <th scope="col" className='text-center'>Total Number of Calls</th>
                                <th scope="col" className='text-center'>Total Duration of Calls</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data && data.length > 0 ? data.map(row => (
                                <tr key={row.import_id}>
                                    <th className='text-end'>{row.import_id}</th>
                                    <td className='text-end'>{row.total_calls_same_continent}</td>
                                    <td className='text-end'>{row.total_duration_same_continent}</td>
                                    <td className='text-end'>{row.total_calls}</td>
                                    <td className='text-end'>{row.total_duration}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="text-center">No data available</td>
                                </tr>  
                            )}
                        </tbody>
                    </table>
                </div>
            </Card>
        </React.Fragment>
    );
}

export default Statistics;