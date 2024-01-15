import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Card from '../../components/Card';
import ModalButton from '../../components/ModalButton';

const UploadedFiles = ({ modalTarget }) => {
    const [data, setData] = useState([]);
    
    useEffect(() => {
        const fetchData = async () => {
            try {
            const response = await axios.get('api/call-uploads/');
            setData(response.data.result);
            } catch (error) {
            console.error('Error fetching data:', error);
            }
        };

        fetchData();

        setInterval(fetchData, 5000);
        }, []);

    const colorStatus = ({ status }) => {
        if (status === 'completed') {
        return 'badge text-bg-success text-uppercase';
        }
        return 'badge text-bg-warning text-uppercase';
    };

    return (
        <React.Fragment>
            <Card>
                <div className="card-header">
                    <span className='fw-semibold'>Uploaded Files</span>
                    <div className='float-end'>
                        <ModalButton label='Upload Calls' target={modalTarget} />
                    </div>
                </div>
                <div className='card-body'>
                    <table className="table table-striped table-hover">
                        <thead>
                            <tr>
                            <th scope="col" className='text-center'>Filename</th>
                            <th scope="col" className='text-center'>Processed/Total</th>
                            <th scope="col" className='text-center'>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data && data.length > 0 ? data.map(row => (
                                <tr key={row.id}>
                                <th>{row.original_filename}</th>
                                <td className='text-end'>{row.processed_count}/{row.total_count}</td>
                                <td><span className={colorStatus(row)}>{row.status}</span></td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="3" className="text-center">No data available</td>
                                </tr>  
                            )}
                        </tbody>
                    </table>
                </div>
            </Card>
        </React.Fragment>
    );
}

export default UploadedFiles;