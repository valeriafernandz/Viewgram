import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { FollowersListPage } from './followers-list';

@NgModule({
  declarations: [
    FollowersListPage,
  ],
  imports: [
    IonicPageModule.forChild(FollowersListPage),
  ],
})
export class FollowersListPageModule {}
