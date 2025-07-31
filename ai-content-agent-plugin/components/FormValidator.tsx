/**
 * Form Validation System for AI Content Agent
 * 
 * Provides consistent form validation, error display, and user feedback
 * across all forms in the application for a unified user experience.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

import React, { useState, useCallback, useRef, useEffect } from 'react';
import { AlertTriangle, CheckCircle, Info, X } from './Icons';

/**
 * Validation rule types
 */
export interface ValidationRule {
    type: 'required' | 'email' | 'url' | 'minLength' | 'maxLength' | 'pattern' | 'custom';
    value?: any;
    message: string;
    validator?: (value: any) => boolean | string;
}

/**
 * Field validation state
 */
export interface FieldValidation {
    isValid: boolean;
    error?: string;
    warning?: string;
    info?: string;
    touched: boolean;
}

/**
 * Form validation state
 */
export interface FormValidationState {
    [fieldName: string]: FieldValidation;
}

/**
 * Validation context
 */
interface ValidationContextType {
    validationState: FormValidationState;
    validateField: (name: string, value: any, rules: ValidationRule[]) => void;
    clearFieldValidation: (name: string) => void;
    markFieldTouched: (name: string) => void;
    isFormValid: () => boolean;
    getFieldError: (name: string) => string | undefined;
    hasFieldError: (name: string) => boolean;
    resetValidation: () => void;
}

const ValidationContext = React.createContext<ValidationContextType | null>(null);

/**
 * Validation provider component - MEDIUM PRIORITY FIX
 */
export const ValidationProvider: React.FC<{
    children: React.ReactNode;
    onValidationChange?: (isValid: boolean, errors: Record<string, string>) => void;
}> = ({ children, onValidationChange }) => {
    const [validationState, setValidationState] = useState<FormValidationState>({});

    const validateField = useCallback((name: string, value: any, rules: ValidationRule[]) => {
        let isValid = true;
        let error: string | undefined;
        let warning: string | undefined;
        let info: string | undefined;

        for (const rule of rules) {
            const result = validateRule(value, rule);
            if (result !== true) {
                isValid = false;
                error = result;
                break;
            }
        }

        setValidationState(prev => {
            const newState = {
                ...prev,
                [name]: {
                    isValid,
                    error,
                    warning,
                    info,
                    touched: prev[name]?.touched || false
                }
            };

            // Notify parent of validation changes
            if (onValidationChange) {
                const errors: Record<string, string> = {};
                let formIsValid = true;
                
                Object.entries(newState).forEach(([fieldName, validation]) => {
                    if (!validation.isValid && validation.error) {
                        errors[fieldName] = validation.error;
                        formIsValid = false;
                    }
                });
                
                onValidationChange(formIsValid, errors);
            }

            return newState;
        });
    }, [onValidationChange]);

    const clearFieldValidation = useCallback((name: string) => {
        setValidationState(prev => {
            const newState = { ...prev };
            delete newState[name];
            return newState;
        });
    }, []);

    const markFieldTouched = useCallback((name: string) => {
        setValidationState(prev => ({
            ...prev,
            [name]: {
                ...prev[name],
                touched: true
            }
        }));
    }, []);

    const isFormValid = useCallback(() => {
        return Object.values(validationState).every(field => field.isValid);
    }, [validationState]);

    const getFieldError = useCallback((name: string) => {
        const field = validationState[name];
        return field?.touched ? field.error : undefined;
    }, [validationState]);

    const hasFieldError = useCallback((name: string) => {
        const field = validationState[name];
        return field?.touched && !field.isValid;
    }, [validationState]);

    const resetValidation = useCallback(() => {
        setValidationState({});
    }, []);

    const contextValue: ValidationContextType = {
        validationState,
        validateField,
        clearFieldValidation,
        markFieldTouched,
        isFormValid,
        getFieldError,
        hasFieldError,
        resetValidation
    };

    return (
        <ValidationContext.Provider value={contextValue}>
            {children}
        </ValidationContext.Provider>
    );
};

/**
 * Hook to use validation context
 */
export const useValidation = () => {
    const context = React.useContext(ValidationContext);
    if (!context) {
        throw new Error('useValidation must be used within a ValidationProvider');
    }
    return context;
};

/**
 * Validate a single rule
 */
const validateRule = (value: any, rule: ValidationRule): true | string => {
    switch (rule.type) {
        case 'required':
            if (!value || (typeof value === 'string' && value.trim() === '')) {
                return rule.message;
            }
            break;

        case 'email':
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return rule.message;
            }
            break;

        case 'url':
            if (value && !/^https?:\/\/.+/.test(value)) {
                return rule.message;
            }
            break;

        case 'minLength':
            if (value && value.length < rule.value) {
                return rule.message;
            }
            break;

        case 'maxLength':
            if (value && value.length > rule.value) {
                return rule.message;
            }
            break;

        case 'pattern':
            if (value && !rule.value.test(value)) {
                return rule.message;
            }
            break;

        case 'custom':
            if (rule.validator) {
                const result = rule.validator(value);
                if (result !== true) {
                    return typeof result === 'string' ? result : rule.message;
                }
            }
            break;
    }

    return true;
};

/**
 * Validated input component
 */
export const ValidatedInput: React.FC<{
    name: string;
    type?: string;
    placeholder?: string;
    value: string;
    onChange: (value: string) => void;
    rules: ValidationRule[];
    label?: string;
    helpText?: string;
    required?: boolean;
    disabled?: boolean;
    className?: string;
    style?: React.CSSProperties;
    validateOnChange?: boolean;
    validateOnBlur?: boolean;
}> = ({
    name,
    type = 'text',
    placeholder,
    value,
    onChange,
    rules,
    label,
    helpText,
    required = false,
    disabled = false,
    className = '',
    style = {},
    validateOnChange = true,
    validateOnBlur = true
}) => {
    const validation = useValidation();
    const inputRef = useRef<HTMLInputElement>(null);
    const [isFocused, setIsFocused] = useState(false);

    const fieldError = validation.getFieldError(name);
    const hasError = validation.hasFieldError(name);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = e.target.value;
        onChange(newValue);
        
        if (validateOnChange) {
            validation.validateField(name, newValue, rules);
        }
    };

    const handleBlur = () => {
        setIsFocused(false);
        validation.markFieldTouched(name);
        
        if (validateOnBlur) {
            validation.validateField(name, value, rules);
        }
    };

    const handleFocus = () => {
        setIsFocused(true);
    };

    // Validate on mount if value exists
    useEffect(() => {
        if (value) {
            validation.validateField(name, value, rules);
        }
    }, []);

    const inputStyles: React.CSSProperties = {
        width: '100%',
        padding: '8px 12px',
        border: `2px solid ${hasError ? '#dc3545' : isFocused ? '#0073aa' : '#ddd'}`,
        borderRadius: '6px',
        fontSize: '14px',
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        transition: 'border-color 0.2s ease, box-shadow 0.2s ease',
        outline: 'none',
        backgroundColor: disabled ? '#f5f5f5' : 'white',
        boxShadow: isFocused ? `0 0 0 3px ${hasError ? '#dc354525' : '#0073aa25'}` : 'none',
        ...style
    };

    return (
        <div className={`aca-validated-input ${className}`} style={{ marginBottom: '16px' }}>
            {label && (
                <label 
                    htmlFor={name}
                    style={{
                        display: 'block',
                        marginBottom: '6px',
                        fontSize: '14px',
                        fontWeight: '600',
                        color: '#333',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    {label}
                    {required && <span style={{ color: '#dc3545', marginLeft: '4px' }}>*</span>}
                </label>
            )}
            
            <div style={{ position: 'relative' }}>
                <input
                    ref={inputRef}
                    id={name}
                    name={name}
                    type={type}
                    placeholder={placeholder}
                    value={value}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    onFocus={handleFocus}
                    disabled={disabled}
                    style={inputStyles}
                    aria-invalid={hasError}
                    aria-describedby={fieldError ? `${name}-error` : helpText ? `${name}-help` : undefined}
                />
                
                {/* Validation icon */}
                {value && (
                    <div style={{
                        position: 'absolute',
                        right: '12px',
                        top: '50%',
                        transform: 'translateY(-50%)',
                        pointerEvents: 'none'
                    }}>
                        {hasError ? (
                            <AlertTriangle style={{ width: '16px', height: '16px', color: '#dc3545' }} />
                        ) : (
                            <CheckCircle style={{ width: '16px', height: '16px', color: '#28a745' }} />
                        )}
                    </div>
                )}
            </div>

            {/* Error message */}
            {fieldError && (
                <div 
                    id={`${name}-error`}
                    role="alert"
                    aria-live="polite"
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                        marginTop: '6px',
                        fontSize: '12px',
                        color: '#dc3545',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    <AlertTriangle style={{ width: '14px', height: '14px', flexShrink: 0 }} />
                    {fieldError}
                </div>
            )}

            {/* Help text */}
            {helpText && !fieldError && (
                <div 
                    id={`${name}-help`}
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                        marginTop: '6px',
                        fontSize: '12px',
                        color: '#666',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    <Info style={{ width: '14px', height: '14px', flexShrink: 0 }} />
                    {helpText}
                </div>
            )}
        </div>
    );
};

/**
 * Validated textarea component
 */
export const ValidatedTextarea: React.FC<{
    name: string;
    placeholder?: string;
    value: string;
    onChange: (value: string) => void;
    rules: ValidationRule[];
    label?: string;
    helpText?: string;
    required?: boolean;
    disabled?: boolean;
    rows?: number;
    className?: string;
    style?: React.CSSProperties;
    validateOnChange?: boolean;
    validateOnBlur?: boolean;
}> = ({
    name,
    placeholder,
    value,
    onChange,
    rules,
    label,
    helpText,
    required = false,
    disabled = false,
    rows = 4,
    className = '',
    style = {},
    validateOnChange = true,
    validateOnBlur = true
}) => {
    const validation = useValidation();
    const [isFocused, setIsFocused] = useState(false);

    const fieldError = validation.getFieldError(name);
    const hasError = validation.hasFieldError(name);

    const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        const newValue = e.target.value;
        onChange(newValue);
        
        if (validateOnChange) {
            validation.validateField(name, newValue, rules);
        }
    };

    const handleBlur = () => {
        setIsFocused(false);
        validation.markFieldTouched(name);
        
        if (validateOnBlur) {
            validation.validateField(name, value, rules);
        }
    };

    const handleFocus = () => {
        setIsFocused(true);
    };

    const textareaStyles: React.CSSProperties = {
        width: '100%',
        padding: '8px 12px',
        border: `2px solid ${hasError ? '#dc3545' : isFocused ? '#0073aa' : '#ddd'}`,
        borderRadius: '6px',
        fontSize: '14px',
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        transition: 'border-color 0.2s ease, box-shadow 0.2s ease',
        outline: 'none',
        backgroundColor: disabled ? '#f5f5f5' : 'white',
        boxShadow: isFocused ? `0 0 0 3px ${hasError ? '#dc354525' : '#0073aa25'}` : 'none',
        resize: 'vertical',
        minHeight: `${rows * 1.5}em`,
        ...style
    };

    return (
        <div className={`aca-validated-textarea ${className}`} style={{ marginBottom: '16px' }}>
            {label && (
                <label 
                    htmlFor={name}
                    style={{
                        display: 'block',
                        marginBottom: '6px',
                        fontSize: '14px',
                        fontWeight: '600',
                        color: '#333',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    {label}
                    {required && <span style={{ color: '#dc3545', marginLeft: '4px' }}>*</span>}
                </label>
            )}
            
            <textarea
                id={name}
                name={name}
                placeholder={placeholder}
                value={value}
                onChange={handleChange}
                onBlur={handleBlur}
                onFocus={handleFocus}
                disabled={disabled}
                rows={rows}
                style={textareaStyles}
                aria-invalid={hasError}
                aria-describedby={fieldError ? `${name}-error` : helpText ? `${name}-help` : undefined}
            />

            {/* Error message */}
            {fieldError && (
                <div 
                    id={`${name}-error`}
                    role="alert"
                    aria-live="polite"
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                        marginTop: '6px',
                        fontSize: '12px',
                        color: '#dc3545',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    <AlertTriangle style={{ width: '14px', height: '14px', flexShrink: 0 }} />
                    {fieldError}
                </div>
            )}

            {/* Help text */}
            {helpText && !fieldError && (
                <div 
                    id={`${name}-help`}
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                        marginTop: '6px',
                        fontSize: '12px',
                        color: '#666',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    <Info style={{ width: '14px', height: '14px', flexShrink: 0 }} />
                    {helpText}
                </div>
            )}
        </div>
    );
};

/**
 * Form validation summary component
 */
export const ValidationSummary: React.FC<{
    showOnlyErrors?: boolean;
    className?: string;
    style?: React.CSSProperties;
}> = ({ showOnlyErrors = true, className = '', style = {} }) => {
    const validation = useValidation();
    const errors: string[] = [];

    Object.entries(validation.validationState).forEach(([fieldName, fieldValidation]) => {
        if (!fieldValidation.isValid && fieldValidation.error && fieldValidation.touched) {
            errors.push(fieldValidation.error);
        }
    });

    if (errors.length === 0) {
        return null;
    }

    return (
        <div 
            className={`aca-validation-summary ${className}`}
            role="alert"
            aria-live="polite"
            style={{
                padding: '12px',
                border: '2px solid #dc3545',
                borderRadius: '6px',
                backgroundColor: '#f8d7da',
                color: '#721c24',
                marginBottom: '16px',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                ...style
            }}
        >
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                <AlertTriangle style={{ width: '16px', height: '16px', color: '#dc3545' }} />
                <strong>Please fix the following errors:</strong>
            </div>
            <ul style={{ margin: 0, paddingLeft: '20px' }}>
                {errors.map((error, index) => (
                    <li key={index} style={{ marginBottom: '4px' }}>
                        {error}
                    </li>
                ))}
            </ul>
        </div>
    );
};

/**
 * Common validation rules
 */
export const ValidationRules = {
    required: (message = 'This field is required'): ValidationRule => ({
        type: 'required',
        message
    }),

    email: (message = 'Please enter a valid email address'): ValidationRule => ({
        type: 'email',
        message
    }),

    url: (message = 'Please enter a valid URL'): ValidationRule => ({
        type: 'url',
        message
    }),

    minLength: (length: number, message?: string): ValidationRule => ({
        type: 'minLength',
        value: length,
        message: message || `Must be at least ${length} characters long`
    }),

    maxLength: (length: number, message?: string): ValidationRule => ({
        type: 'maxLength',
        value: length,
        message: message || `Must be no more than ${length} characters long`
    }),

    pattern: (regex: RegExp, message: string): ValidationRule => ({
        type: 'pattern',
        value: regex,
        message
    }),

    apiKey: (message = 'Please enter a valid API key'): ValidationRule => ({
        type: 'pattern',
        value: /^[a-zA-Z0-9_\-\.]+$/,
        message
    }),

    custom: (validator: (value: any) => boolean | string, message: string): ValidationRule => ({
        type: 'custom',
        validator,
        message
    })
};

export default ValidationProvider;