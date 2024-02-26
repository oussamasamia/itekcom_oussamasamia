# Module Name: itekcom_oussamasamia
#### Tested on PrestaShop 8.1.4

## Description
The Itekcom Test module enhances the checkout process in PrestaShop by adding a health step. During this step, users can input their personal doctor information, which is then stored in the database. Additionally, the module provides a convenient option for users to sign in with GitHub. If the user already exists, they will be signed in automatically. If not, the module will handle the sign-up process and then sign in the user.
## Compatibility
- PrestaShop version: 1.6 to 8.1.4

## Installation
1. Download the `itekcom_oussamasamia` module zip file.
2. Extract the contents of the zip file.
3. Upload the extracted `itekcom_oussamasamia` folder to the `modules` directory of your PrestaShop installation.
4. Install the module through the PrestaShop back office by navigating to Modules and Services > Modules and Services. Search for "itekcom_oussamasamia" and click "Install".

## Usage (Health Information)
1. After installation, navigate to the checkout process in your PrestaShop store.
2. Proceed through the checkout steps until you reach the health step.
3. In the health step, you will find a form where you can input your personal doctor information.
4. Fill out the form with the required information and proceed with the checkout process.

## Usage (Express sign-in Github)
The Itekcom Test module offers an express sign-in feature using GitHub authentication. This allows users to quickly sign in to their accounts using their GitHub credentials. Here's how to use it:

1. Module Configuration:

- In the PrestaShop back office, navigate to the Itekcom Test module configuration page.
- Enter your GitHub OAuth application client ID and client secret. If you don't have these credentials yet, you can create a new OAuth application on GitHub following the documentation.
- Save the configuration.

2. GitHub Sign-In Button:

- Once the module is configured with valid GitHub OAuth credentials, a "Sign in with GitHub" button will be displayed on the login or authentication page.

3. Signing In:

- Users can click on the "Sign in with GitHub" button to initiate the GitHub authentication process.
- They will be redirected to GitHub, where they can log in with their GitHub credentials.

4. Authorization:

- After logging in, users will be asked to authorize the application to access their GitHub account information.

5. Redirect Back:

- Once authorized, users will be redirected back to your PrestaShop store.
6. Account Creation/Sign-In:

- If the user already has an account linked to their GitHub email, they will be signed in automatically.
- If the user does not have an existing account, one will be created using their GitHub email and basic profile information. They will then be signed in to the newly created account.
7. Authentication Completed:

- The user is now signed in to their PrestaShop account using their GitHub credentials.

## Uninstallation
1. Log in to your PrestaShop back office.
2. Navigate to Modules and Services > Modules and Services.
3. Search for "itekcom_oussamasamia" in the list of installed modules.
4. Click the "Uninstall" button next to the module.
5. Confirm the uninstallation.

## Support
For any issues or questions regarding the Itekcom module, please contact our support team at oussamasamia1pro@gmail.com

## Credits
- Developed by Oussama SAMIA
- Logo design by Itekcom
