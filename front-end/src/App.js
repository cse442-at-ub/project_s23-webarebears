import React, { Component } from 'react';
import { BrowserRouter as Router, Routes, Route}
    from 'react-router-dom';
import './App.css';
import Register from './pages/register';
import Home from './pages/home';
import Login from './pages/login'

function App() {
  return (
    <Router>
      <Routes>

        <Route exact path='/' element={<Home/>} />
        <Route path='/register' element={<Register/>} />
        <Route path = "/login" element = {<Login/>} />
        
      </Routes>
    </Router>
  );
}

export default App;
