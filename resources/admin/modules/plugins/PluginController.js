import { capitalize } from "vue";

import Controller from "../Controller";

class PluginController extends Controller {
  /**
   * Transform the response according to the mapping
   * casts Object
   */
  casts = {
    id: "int",
  };

  /**
   * Query strings to pass with all requests.
   * query Object
   */
  query = {
    orderby: "id",
    order: "desc",
  };

  /**
   * Set the request headers with every http request
   * headers Object
   */
  headers = {
    "X-Custom-Controller-Header-1": 1,
    "X-Custom-Controller-Header-2": 2,
  };
}

export default PluginController.init();
