import React from 'react';
import { Route, Routes } from 'react-router-dom';

import Home from '../pages/Home';

const Body = () => {
  return (
    <div className='container-fluid'>
      <Routes>
        <Route path="/" element={<Home />} />
      </Routes>
    </div>
  );
};

export default Body;
