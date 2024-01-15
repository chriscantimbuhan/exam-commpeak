import React from 'react';

// import logo from './logo.svg';
import './App.css';

import { BrowserRouter as Router } from 'react-router-dom';
import Header from './templates/Header';
import Body from './templates/Body';

// window.axios = axios;

function App() {
  return (
    <Router>
        <Header />
        <Body />
    </Router>
  );
}

export default App;
