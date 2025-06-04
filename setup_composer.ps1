# Download Composer Setup
Invoke-WebRequest -Uri https://getcomposer.org/Composer-Setup.exe -OutFile composer-setup.exe

# Install Composer silently
Start-Process -FilePath "composer-setup.exe" -ArgumentList "/SILENT" -Wait

# Refresh environment variables
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

# Install Swift Mailer
composer install

# Clean up
Remove-Item composer-setup.exe 