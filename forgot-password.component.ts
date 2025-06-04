import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss']
})
export class ForgotPasswordComponent implements OnInit {
  step: 'email' | 'reset' = 'email';
  emailForm!: FormGroup;
  resetForm!: FormGroup;
  errorMessage: string = '';
  successMessage: string = '';
  loading: boolean = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient
  ) {}

  ngOnInit(): void {
    this.initializeForms();
  }

  private initializeForms(): void {
    this.emailForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]]
    });

    this.resetForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      otp: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(6)]],
      newPassword: ['', [
        Validators.required,
        Validators.minLength(8),
        Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/)
      ]],
      confirmPassword: ['', [Validators.required]]
    }, { validator: this.passwordMatchValidator });
  }

  private passwordMatchValidator(group: FormGroup): void {
    const password = group.get('newPassword')?.value;
    const confirmPassword = group.get('confirmPassword')?.value;
    
    if (password !== confirmPassword) {
      group.get('confirmPassword')?.setErrors({ passwordMismatch: true });
    } else {
      group.get('confirmPassword')?.setErrors(null);
    }
  }

  onSendOTP(): void {
    if (this.emailForm.valid) {
      this.loading = true;
      this.errorMessage = '';
      this.successMessage = '';

      const email = this.emailForm.get('email')?.value;

      this.http.post<any>('/api/forgot-password/send-otp', {
        email: email
      }).subscribe({
        next: (response) => {
          this.loading = false;
          this.successMessage = 'OTP đã được gửi đến email của bạn';
          this.step = 'reset';
          this.resetForm.patchValue({ 
            email: this.emailForm.get('email')?.value 
          });
        },
        error: (error) => {
          this.loading = false;
          this.errorMessage = error.error?.message || 'Không thể gửi OTP. Vui lòng thử lại sau.';
        }
      });
    } else {
      this.emailForm.markAllAsTouched();
    }
  }

  onResetPassword(): void {
    if (this.resetForm.valid) {
      this.loading = true;
      this.errorMessage = '';
      this.successMessage = '';

      const formData = {
        email: this.resetForm.get('email')?.value,
        otp: this.resetForm.get('otp')?.value,
        newPassword: this.resetForm.get('newPassword')?.value
      };

      this.http.post<any>('/api/forgot-password/reset', formData).subscribe({
        next: (response) => {
          this.loading = false;
          this.successMessage = 'Mật khẩu đã được đặt lại thành công';
        },
        error: (error) => {
          this.loading = false;
          this.errorMessage = error.error?.message || 'Không thể đặt lại mật khẩu. Vui lòng thử lại.';
        }
      });
    } else {
      this.resetForm.markAllAsTouched();
    }
  }

  getErrorMessage(formGroup: FormGroup, controlName: string): string {
    const control = formGroup.get(controlName);
    if (control?.errors && control.touched) {
      if (control.errors['required']) return 'Trường này là bắt buộc';
      if (control.errors['email']) return 'Email không hợp lệ';
      if (control.errors['minlength']) return `Độ dài tối thiểu là ${control.errors['minlength'].requiredLength} ký tự`;
      if (control.errors['maxlength']) return `Độ dài tối đa là ${control.errors['maxlength'].requiredLength} ký tự`;
      if (control.errors['pattern']) return 'Mật khẩu phải chứa chữ hoa, chữ thường và số';
      if (control.errors['passwordMismatch']) return 'Mật khẩu không khớp';
    }
    return '';
  }
} 