import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import Modal from '../../components/Modal';
import Form from '../../components/Form';
import Input from '../../components/Input';

const UploadedFileModal = ({modalTarget }) => {
    const [formData, setFormData] = useState({
        customer_call_file: null
    });
    const [errors, setErrors] = useState([]);
    const [disabled, setDisabled] = useState('');
    const [showAlert, setShowAlert] = useState(false);
    const fileInputRef = useRef(null);

const handleInputChange = (event) => {
    const { name, value, type } = event.target;
    const inputValue = type === 'file' ? event.target.files[0] : value;

    setFormData({
      ...formData,
      [name]: inputValue,
    });
  };

const handleUpload = () => {
    setDisabled('disabled');

    axios.post('api/customer-calls/process-upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            }
    })
    .then(response => {
        setShowAlert(true);
        setErrors([]);
        setDisabled('');
        resetFileInput();
    }).catch(error => {
        setDisabled('');
        setErrors(error.response.data.errors);
        resetFileInput();
    });
};

useEffect(() => {
    const timeoutId = setTimeout(() => {
        if (showAlert) {
            setShowAlert(false);
        }
    }, 5000);

    return () => clearTimeout(timeoutId);
  }, [showAlert]); 

  const resetFileInput = () => {
    if (fileInputRef.current) {
        setFormData({
            ...formData,
            customer_call_file: null
          });

        fileInputRef.current.value = null;
      }
  };

    return (
        <React.Fragment>
            <Modal target={modalTarget}>
                <div className="modal-header">
                    <h1 className="modal-title fs-5" id={modalTarget + "Label"}>Upload Calls</h1>
                </div>
                <div className="modal-body">
                {showAlert && (
                    <div className='alert alert-success' role="alert">
                        Success! File is being uploaded is the background.
                    </div>
                )}
                    <Form encType='multipart/form-data' id={modalTarget + "Form"}>
                        <Input
                            type='file'
                            id={modalTarget + 'File'}
                            label='Only accepts CSV and TXT file types.'
                            onChange={handleInputChange}
                            name='customer_call_file'
                            errors={errors}
                            reference={fileInputRef}
                        />
                    </Form>
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button
                        type="button"
                        className={`btn btn-sm btn-primary ${disabled}`} 
                        id={modalTarget + "Button"}
                        onClick={handleUpload}
                    >Upload</button>
                </div>
            </Modal>
        </React.Fragment>
    );
}

export default UploadedFileModal;
