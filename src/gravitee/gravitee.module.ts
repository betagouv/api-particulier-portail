import { HttpModule, Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { GraviteeClient } from 'src/gravitee/client/client';
import { SubscriptionService } from 'src/gravitee/subscription.service';

@Module({
  imports: [ConfigModule.forRoot(), HttpModule],
  providers: [GraviteeClient, SubscriptionService],
  exports: [SubscriptionService],
})
export class GraviteeModule {}
