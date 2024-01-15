import React from 'react';
import UploadedFiles from '../page_components/Home/UploadedFiles';
import UploadedFileModal from '../page_components/Home/UploadFileModal';
import Statistics from '../page_components/Home/Statistics';


const Home = () => {
    const modalTarget = 'uploadCallModal';

    return (
        <React.Fragment>
            <div className='row'>
                <div className='col-7'>
                    <Statistics modalTarget={modalTarget}/>
                </div>
                <div className='col-5'>
                    <UploadedFiles modalTarget={modalTarget}/>
                </div>
            </div>

            <UploadedFileModal modalTarget={modalTarget}/>
        </React.Fragment>
    );
  };
  
  export default Home;