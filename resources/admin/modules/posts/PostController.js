import { capitalize } from 'vue';

import Controller from '../Controller';

class PostController extends Controller {
    /**
     * Transform the response according to the mapping
     * casts Object
     */
    casts = {
        'ID': 'int',
        'total': 'int',
        'per_page': 'int',
        'current_page': 'int',
        'post_title': capitalize,
        'post_date': 'datetime:D, jS M, Y h:i:s A',
        'posts.comments.comment_date': 'datetime:d-m-Y H:i:s A',
    };

    /**
     * Query strings to pass with all requests.
     * query Object
     */
    query = {
        'orderby': 'ID',
        'order': 'desc',
    };

    /**
     * Set the request headers with every http request
     * headers Object
     */
    headers = {
        'X-Custom-Controller-Header-1': 1,
        'X-Custom-Controller-Header-2': 2,
    };
}

export default PostController.init();
