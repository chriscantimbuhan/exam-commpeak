import React, { useEffect, useState } from 'react';

const Input = ({ type, id, label, name, errors, reference, onChange }) => {
    const [errorString, setErrorString] = useState('');
    const [inputBorderColor, setInputBorderColor] = useState('');
    
    // Use useEffect to update state based on errors
    useEffect(() => {
        if (errors[name]) {
        setErrorString(errors[name]);
        setInputBorderColor('border border-danger');
        } else {
        setErrorString('');
        setInputBorderColor('');
        }
    }, [errors, name]);

    return (
        <React.Fragment>
            <div className='mb-3'>
                <label for="exampleInputEmail1" className="form-label">{label}</label>
                <input className={`form-control ${inputBorderColor}`} type={type} id={id} name={name} onChange={onChange} ref={reference} />
                {errorString && <div className='text-danger'>{errorString}</div>}
            </div>
        </React.Fragment>
    );
}

export default Input;