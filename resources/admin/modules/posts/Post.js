import Model from '@/modules/Model';
import { useRoute } from 'vue-router';
import PostController from './PostController';
import { capitalize, computed, getCurrentInstance } from 'vue';

class Post extends Model {
	data = {
		list: [],
		form: {},
    	errors: {},
        status: 'all',
    	pagination: {
        	current_page: 1,
        	per_page: 2,
        	total: 0,
        },
        isVisible: {
            form: false,
        },
        isBusy: {
            saving: false,
            loading: false,
        },
        instance: null
    };

    async get() {
        this.isBusy.loading = true;
        
        const response = await PostController.withQuery({
            ...this.useQuery()
        }).get();

        this.isBusy.loading = false;

        this.list = response.data;
        this.pagination.total = response.total;
        this.pagination.current_page = response.current_page;
    }

    async find(id) {
        this.instance = await PostController.find(id);
    }

    async save() {
        const method = this.form.ID ? 'update' : 'store';

        const response = await PostController.withParams(
            this.form
        )[method](this.form.ID);

        this.hideForm();
        
        this.get();
    }

    async delete(data) {
        PostController.delete(data.ID).then(this.get);
    }
    
    mapFormData(data) {
        return {
            ID: data?.ID,
            post_title: data?.post_title,
            post_content: data?.post_content
        }
    }

    edit(data) {
        this.showForm(data);
    }

    showForm(data = {}) {
        this.form = this.mapFormData(data);
        this.isVisible.form = true;
    }

    hideForm() {
        this.errors = {};
        this.isVisible.form = false;
    }

    useQuery() {
        const { 
            query: {
                status = this.status,
                per_page = this.pagination.per_page,
                page = this.pagination.current_page
            } = {} 
        } = getCurrentInstance() ? useRoute() : {};

        this.status = status;
        this.pagination.per_page = per_page;
        this.pagination.current_page = page;

        return { status, page, per_page };
    }

    /**
     * Dynamic property accessor for full name
     * @param  string value
     * @return string
     */
    getFullNameAttribute(value) {
        return `Welcome! ${value}`;
    }

    /**
     * Dynamic property mutator for full name
     * @param string key
     * @param string value
     */
    setFullNameAttribute(key, value) {
        this.data[key] = value.split(' ').map(
            i => capitalize(i)
        ).join(' ');
    }
}

export default Post;
