import { Module } from '@nestjs/common';
import { AuthenticationModule } from 'src/authentication/authentication.module';
import { AppController } from './app.controller';

@Module({
  imports: [AuthenticationModule],
  controllers: [AppController],
})
export class AppModule {}
