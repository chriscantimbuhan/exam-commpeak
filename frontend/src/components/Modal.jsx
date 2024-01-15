import React from 'react';

const Modal = ({ target, children }) => {
    return (
        <React.Fragment>
            <div className="modal fade" id={target} tabindex="-1" aria-labelledby={target} aria-hidden="true">
                <div className="modal-dialog">
                    <div className="modal-content">
                        {children}
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default Modal;
