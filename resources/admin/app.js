import app from '@/bootstrap';
import { capitalize } from 'vue';
import {ElLoading, ElNotification, ElMessageBox } from 'element-plus';

const globals = app.config.globalProperties;

globals.$confirm = ElMessageBox.confirm;

globals.$notify = (message, type = 'info') => ElNotification({
	offset: 20,
	type: type,
    title: type === 'info' ? 'Notification': capitalize(type),
	message: message
});

globals.$notifySuccess = (message) => globals.$notify(message, 'success');

globals.$notifyWarning = (message) => globals.$notify(message, 'warning');

globals.$notifyError = (message) => globals.$notify(message, 'error');

app.use(ElLoading).mount('#fluent-framework-app');

window.addEventListener('offline', () => {
  	ElNotification({
		type: 'warning',
		position: 'bottom-right',
		message: 'You are currently offline.'
	});
});

window.addEventListener('online', () => {
  	ElNotification({
		type: 'success',
		position: 'bottom-right',
		message: 'Your connection has been restored.'
	});
});
