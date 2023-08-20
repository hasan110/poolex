import LoaderPanel from '../../pages/LoaderPanel.vue';
import Dashboard from '../../pages/Dashboard.vue';
import DataChart from '../../pages/DataChart.vue';
import ViewRanking from '../../pages/views/ViewRanking.vue';

import Users from '../../pages/users/Users.vue';
import SellerUsers from '../../pages/users/SellerUsers.vue';
import UserAwards from '../../pages/users/UserAwards.vue';
import User from '../../pages/users/User.vue';

import Admins from '../../pages/admin/Admins.vue';

import Harvests from '../../pages/harvests/Harvests.vue';
import Harvest from '../../pages/harvests/Harvest.vue';

import Blogs from '../../pages/blogs/Blogs.vue';
import CreateBlog from '../../pages/blogs/CreateBlog.vue';
import EditBlog from '../../pages/blogs/EditBlog.vue';

import Stores from '../../pages/stores/Stores.vue';
import StoreProducts from '../../pages/stores/StoreProducts.vue';
import Invoices from '../../pages/invoices/Invoices.vue';

import NotResponsedTickets from '../../pages/tickets/NotResponsedTickets.vue';
import ResponsedTickets from '../../pages/tickets/ResponsedTickets.vue';
import ClosedTickets from '../../pages/tickets/ClosedTickets.vue';
import Ticket from '../../pages/tickets/Ticket.vue';

import Notification from '../../pages/views/Notification.vue';

import Slider from '../../pages/views/Slider.vue';
import Advertise from '../../pages/views/Advertise.vue';
import Video from '../../pages/views/Video.vue';
import VideoApi from '../../pages/views/VideoApi.vue';
import Category from '../../pages/views/Category.vue';
import SlideShow from '../../pages/views/SlideShow.vue';
import Help from '../../pages/views/Help.vue';
import Rule from '../../pages/views/Rule.vue';
import Message from '../../pages/views/Message.vue';
import Award from '../../pages/views/Award.vue';
import Plan from '../../pages/views/Plan.vue';
import Store from '../../pages/views/Store.vue';
import Setting from '../../pages/views/Setting.vue';

const allUrl = [{
    path: '/',
    component: LoaderPanel,
    redirect: '/Dashboard',
    children: [
        { path: 'Dashboard', name: 'Dashboard', component: Dashboard },
        { path: 'DataChart/:section', name: 'DataChart', component: DataChart },
        { path: 'ViewRanking', name: 'ViewRanking', component: ViewRanking },

        { path: 'Users', name: 'Users', component: Users },
        { path: 'SellerUsers', name: 'SellerUsers', component: SellerUsers },
        { path: 'UserAwards', name: 'UserAwards', component: UserAwards },
        { path: 'User/:id', name: 'User', component: User },

        { path: 'Admins', name: 'Admins', component: Admins },

        { path: 'Harvests', name: 'Harvests', component: Harvests },
        { path: 'Harvest/:id', name: 'Harvest', component: Harvest },

        { path: 'Blogs', name: 'Blogs', component: Blogs },
        { path: 'CreateBlog', name: 'CreateBlog', component: CreateBlog },
        { path: 'EditBlog/:id', name: 'EditBlog', component: EditBlog },

        { path: 'Stores', name: 'Stores', component: Stores },
        { path: 'StoreProducts', name: 'StoreProducts', component: StoreProducts },
        { path: 'Invoices', name: 'Invoices', component: Invoices },

        { path: 'NotResponsedTickets', name: 'NotResponsedTickets', component: NotResponsedTickets },
        { path: 'ResponsedTickets', name: 'ResponsedTickets', component: ResponsedTickets },
        { path: 'ClosedTickets', name: 'ClosedTickets', component: ClosedTickets },
        { path: 'Ticket/:id', name: 'Ticket', component: Ticket },

        { path: 'Notification', name: 'Notification', component: Notification },

        { path: 'Slider', name: 'Slider', component: Slider },
        { path: 'Advertise', name: 'Advertise', component: Advertise },
        { path: 'Video', name: 'Video', component: Video },
        { path: 'VideoApi', name: 'VideoApi', component: VideoApi },
        { path: 'Category', name: 'Category', component: Category },
        { path: 'SlideShow', name: 'SlideShow', component: SlideShow },
        { path: 'Help', name: 'Help', component: Help },
        { path: 'Rule', name: 'Rule', component: Rule },
        { path: 'Message', name: 'Message', component: Message },
        { path: 'Award', name: 'Award', component: Award },
        { path: 'Plan', name: 'Plan', component: Plan },
        { path: 'Store', name: 'Store', component: Store },
        { path: 'Setting', name: 'Setting', component: Setting },
    ]
}];
export default allUrl;
