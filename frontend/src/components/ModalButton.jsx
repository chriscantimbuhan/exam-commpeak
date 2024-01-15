import React from 'react';

const ModalButton = ({ label, target }) => {
    return (
        <React.Fragment>
            <button type="button" className="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target={"#" + target}>
                {label}
            </button>
        </React.Fragment>
    );
};

export default ModalButton
